<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchService
{
    /**
     * Enhanced product search with filters
     */
    public function searchProducts(Request $request)
    {
        $query = Product::with(['category', 'approvedReviews'])
            ->where('status', 'active');

        // Text search in name, description, and reviews
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('short_description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('approvedReviews', function($reviewQuery) use ($searchTerm) {
                      $reviewQuery->where('title', 'like', "%{$searchTerm}%")
                                  ->orWhere('review', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            if (is_array($request->category)) {
                $query->whereIn('category_id', $request->category);
            } else {
                $query->where('category_id', $request->category);
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->whereHas('approvedReviews', function($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }

        // Availability filter
        if ($request->filled('in_stock') && $request->in_stock) {
            $query->where('stock_quantity', '>', 0);
        }

        // Brand filter (if you have brands)
        if ($request->filled('brand')) {
            if (is_array($request->brand)) {
                $query->whereIn('brand', $request->brand);
            } else {
                $query->where('brand', $request->brand);
            }
        }

        // Verified reviews filter
        if ($request->filled('verified_reviews') && $request->verified_reviews) {
            $query->whereHas('approvedReviews', function($q) {
                $q->where('is_verified_purchase', true);
            });
        }

        // Add rating calculations
        $query->withAvg('approvedReviews', 'rating')
              ->withCount('approvedReviews');

        // Sorting
        $this->applySorting($query, $request->get('sort', 'relevance'));

        return $query;
    }

    /**
     * Apply sorting to search results
     */
    private function applySorting($query, $sort)
    {
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('approved_reviews_avg_rating', 'desc')
                      ->orderBy('approved_reviews_count', 'desc');
                break;
            case 'reviews':
                $query->orderBy('approved_reviews_count', 'desc')
                      ->orderBy('approved_reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')
                      ->orderBy('order_items_count', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default: // relevance
                $query->orderBy('approved_reviews_avg_rating', 'desc')
                      ->orderBy('approved_reviews_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }
    }

    /**
     * Get search suggestions/autocomplete
     */
    public function getSearchSuggestions($term, $limit = 10)
    {
        $suggestions = [];

        // Product name suggestions
        $productSuggestions = Product::where('name', 'like', "%{$term}%")
            ->where('status', 'active')
            ->select('name')
            ->distinct()
            ->take($limit)
            ->pluck('name')
            ->toArray();

        $suggestions = array_merge($suggestions, $productSuggestions);

        // Category suggestions
        $categorySuggestions = DB::table('categories')
            ->where('name', 'like', "%{$term}%")
            ->select('name')
            ->distinct()
            ->take(5)
            ->pluck('name')
            ->toArray();

        $suggestions = array_merge($suggestions, $categorySuggestions);

        // Brand suggestions (if you have brands)
        $brandSuggestions = Product::where('brand', 'like', "%{$term}%")
            ->where('status', 'active')
            ->select('brand')
            ->distinct()
            ->take(5)
            ->pluck('brand')
            ->filter()
            ->toArray();

        $suggestions = array_merge($suggestions, $brandSuggestions);

        return array_slice(array_unique($suggestions), 0, $limit);
    }

    /**
     * Search within product reviews
     */
    public function searchReviews($term, $productId = null)
    {
        $query = ProductReview::with(['user', 'product'])
            ->where('is_approved', true)
            ->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('review', 'like', "%{$term}%");
            });

        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->orderBy('helpful_count', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Get filter options for search results
     */
    public function getFilterOptions($searchQuery = null)
    {
        $baseQuery = Product::where('status', 'active');
        
        if ($searchQuery) {
            $baseQuery->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        $options = [];

        // Price range
        $priceRange = $baseQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $options['price_range'] = [
            'min' => floor($priceRange->min_price ?? 0),
            'max' => ceil($priceRange->max_price ?? 1000)
        ];

        // Categories
        $options['categories'] = DB::table('categories')
            ->whereIn('id', function($query) use ($baseQuery) {
                $query->select('category_id')
                      ->from('products')
                      ->whereIn('id', $baseQuery->pluck('id'));
            })
            ->select('id', 'name')
            ->get();

        // Brands
        $options['brands'] = $baseQuery->select('brand')
            ->distinct()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->orderBy('brand')
            ->pluck('brand');

        // Rating options
        $options['ratings'] = [
            ['value' => 4, 'label' => '4 Stars & Up'],
            ['value' => 3, 'label' => '3 Stars & Up'],
            ['value' => 2, 'label' => '2 Stars & Up'],
            ['value' => 1, 'label' => '1 Star & Up']
        ];

        return $options;
    }

    /**
     * Get popular search terms
     */
    public function getPopularSearchTerms($limit = 10)
    {
        // This would typically come from search analytics
        // For now, return most reviewed product names
        return Product::withCount('approvedReviews')
            ->where('status', 'active')
            ->orderBy('approved_reviews_count', 'desc')
            ->take($limit)
            ->pluck('name');
    }

    /**
     * Advanced search with multiple criteria
     */
    public function advancedSearch(array $criteria)
    {
        $query = Product::with(['category', 'approvedReviews'])
            ->where('status', 'active');

        // Multiple search terms (AND logic)
        if (!empty($criteria['search_terms'])) {
            foreach ($criteria['search_terms'] as $term) {
                $query->where(function($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('description', 'like', "%{$term}%");
                });
            }
        }

        // Exclude terms
        if (!empty($criteria['exclude_terms'])) {
            foreach ($criteria['exclude_terms'] as $term) {
                $query->where(function($q) use ($term) {
                    $q->where('name', 'not like', "%{$term}%")
                      ->where('description', 'not like', "%{$term}%");
                });
            }
        }

        // Exact phrase search
        if (!empty($criteria['exact_phrase'])) {
            $query->where(function($q) use ($criteria) {
                $q->where('name', 'like', "%{$criteria['exact_phrase']}%")
                  ->orWhere('description', 'like', "%{$criteria['exact_phrase']}%");
            });
        }

        // Date range
        if (!empty($criteria['date_from'])) {
            $query->where('created_at', '>=', $criteria['date_from']);
        }
        if (!empty($criteria['date_to'])) {
            $query->where('created_at', '<=', $criteria['date_to']);
        }

        // Stock status
        if (isset($criteria['stock_status'])) {
            if ($criteria['stock_status'] === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($criteria['stock_status'] === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        return $query->withAvg('approvedReviews', 'rating')
                     ->withCount('approvedReviews');
    }
}
