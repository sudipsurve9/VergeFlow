<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\RecentlyViewed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Get "Customers who bought this also bought" recommendations
     */
    public function getCustomersAlsoBought(Product $product, $limit = 8)
    {
        // Get orders that contain this product
        $orders_with_product = Order::whereHas('orderItems', function($query) use ($product) {
            $query->where('product_id', $product->id);
        })->pluck('id');

        if ($orders_with_product->isEmpty()) {
            return $this->getFallbackRecommendations($product, $limit);
        }

        // Get other products from those orders
        $recommended_products = Product::whereHas('orderItems', function($query) use ($orders_with_product) {
            $query->whereIn('order_id', $orders_with_product);
        })
        ->where('id', '!=', $product->id)
        ->where('status', 'active')
        ->withCount(['orderItems' => function($query) use ($orders_with_product) {
            $query->whereIn('order_id', $orders_with_product);
        }])
        ->withAvg('approvedReviews', 'rating')
        ->orderBy('order_items_count', 'desc')
        ->orderBy('approved_reviews_avg_rating', 'desc')
        ->take($limit)
        ->get();

        // If not enough recommendations, fill with category-based suggestions
        if ($recommended_products->count() < $limit) {
            $additional = $this->getCategoryBasedRecommendations(
                $product, 
                $limit - $recommended_products->count(),
                $recommended_products->pluck('id')->toArray()
            );
            $recommended_products = $recommended_products->merge($additional);
        }

        return $recommended_products;
    }

    /**
     * Get "Frequently bought together" recommendations
     */
    public function getFrequentlyBoughtTogether(Product $product, $limit = 3)
    {
        // Get products that appear together in the same orders
        $frequently_together = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->join('products', 'oi2.product_id', '=', 'products.id')
            ->where('oi1.product_id', $product->id)
            ->where('oi2.product_id', '!=', $product->id)
            ->where('products.status', 'active')
            ->select('products.*', DB::raw('COUNT(*) as frequency'))
            ->groupBy('products.id')
            ->orderBy('frequency', 'desc')
            ->take($limit)
            ->get();

        return $frequently_together;
    }

    /**
     * Get personalized recommendations for a user
     */
    public function getPersonalizedRecommendations(User $user, $limit = 12)
    {
        $recommendations = collect();

        // 1. Based on purchase history (40% weight)
        $purchase_based = $this->getPurchaseBasedRecommendations($user, ceil($limit * 0.4));
        $recommendations = $recommendations->merge($purchase_based);

        // 2. Based on recently viewed (30% weight)
        $view_based = $this->getViewBasedRecommendations($user, ceil($limit * 0.3));
        $recommendations = $recommendations->merge($view_based);

        // 3. Based on cart items (20% weight)
        $cart_based = $this->getCartBasedRecommendations($user, ceil($limit * 0.2));
        $recommendations = $recommendations->merge($cart_based);

        // 4. Trending products (10% weight)
        $trending = $this->getTrendingProducts(ceil($limit * 0.1));
        $recommendations = $recommendations->merge($trending);

        // Remove duplicates and limit results
        return $recommendations->unique('id')->take($limit);
    }

    /**
     * Get recommendations based on purchase history
     */
    private function getPurchaseBasedRecommendations(User $user, $limit)
    {
        // Get categories from user's purchase history
        $purchased_categories = $user->orders()
            ->with('orderItems.product')
            ->get()
            ->pluck('orderItems.*.product.category_id')
            ->flatten()
            ->unique();

        if ($purchased_categories->isEmpty()) {
            return collect();
        }

        // Get products from those categories that user hasn't bought
        $purchased_product_ids = $user->orders()
            ->with('orderItems')
            ->get()
            ->pluck('orderItems.*.product_id')
            ->flatten()
            ->unique();

        return Product::whereIn('category_id', $purchased_categories)
            ->whereNotIn('id', $purchased_product_ids)
            ->where('status', 'active')
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recommendations based on recently viewed products
     */
    private function getViewBasedRecommendations(User $user, $limit)
    {
        $recently_viewed = RecentlyViewed::where('user_id', $user->id)
            ->with('product')
            ->orderBy('viewed_at', 'desc')
            ->take(5)
            ->get();

        if ($recently_viewed->isEmpty()) {
            return collect();
        }

        $viewed_categories = $recently_viewed->pluck('product.category_id')->unique();
        $viewed_product_ids = $recently_viewed->pluck('product_id');

        return Product::whereIn('category_id', $viewed_categories)
            ->whereNotIn('id', $viewed_product_ids)
            ->where('status', 'active')
            ->withAvg('approvedReviews', 'rating')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recommendations based on cart items
     */
    private function getCartBasedRecommendations(User $user, $limit)
    {
        $cart_items = CartItem::where('user_id', $user->id)
            ->with('product')
            ->get();

        if ($cart_items->isEmpty()) {
            return collect();
        }

        $recommendations = collect();
        
        foreach ($cart_items as $cart_item) {
            $also_bought = $this->getCustomersAlsoBought($cart_item->product, 2);
            $recommendations = $recommendations->merge($also_bought);
        }

        return $recommendations->unique('id')->take($limit);
    }

    /**
     * Get trending products
     */
    private function getTrendingProducts($limit)
    {
        // Products with most orders in the last 30 days
        return Product::whereHas('orderItems', function($query) {
            $query->whereHas('order', function($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(30));
            });
        })
        ->withCount(['orderItems' => function($query) {
            $query->whereHas('order', function($orderQuery) {
                $orderQuery->where('created_at', '>=', now()->subDays(30));
            });
        }])
        ->where('status', 'active')
        ->orderBy('order_items_count', 'desc')
        ->take($limit)
        ->get();
    }

    /**
     * Get category-based recommendations
     */
    private function getCategoryBasedRecommendations(Product $product, $limit, $exclude_ids = [])
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereNotIn('id', $exclude_ids)
            ->where('status', 'active')
            ->withAvg('approvedReviews', 'rating')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get fallback recommendations when no data available
     */
    private function getFallbackRecommendations(Product $product, $limit)
    {
        // Return popular products from same category
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->withCount('orderItems')
            ->withAvg('approvedReviews', 'rating')
            ->orderBy('order_items_count', 'desc')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get similar products based on attributes
     */
    public function getSimilarProducts(Product $product, $limit = 6)
    {
        $query = Product::where('id', '!=', $product->id)
            ->where('status', 'active');

        // Same category gets highest priority
        $query->where('category_id', $product->category_id);

        // Similar price range (±20%)
        $price_min = $product->price * 0.8;
        $price_max = $product->price * 1.2;
        $query->whereBetween('price', [$price_min, $price_max]);

        return $query->withAvg('approvedReviews', 'rating')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recommendations for homepage
     */
    public function getHomepageRecommendations(User $user = null, $sections = [])
    {
        $recommendations = [];

        if ($user) {
            // Personalized sections for logged-in users
            $recommendations['for_you'] = $this->getPersonalizedRecommendations($user, 8);
            $recommendations['based_on_history'] = $this->getPurchaseBasedRecommendations($user, 6);
            $recommendations['recently_viewed'] = $this->getRecentlyViewedProducts($user, 6);
        }

        // General sections for all users
        $recommendations['trending'] = $this->getTrendingProducts(8);
        $recommendations['top_rated'] = $this->getTopRatedProducts(8);
        $recommendations['new_arrivals'] = $this->getNewArrivals(8);
        $recommendations['best_sellers'] = $this->getBestSellers(8);

        return $recommendations;
    }

    /**
     * Get recently viewed products for user
     */
    private function getRecentlyViewedProducts(User $user, $limit)
    {
        return Product::whereHas('recentlyViewed', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['recentlyViewed' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->orderBy('recently_viewed.viewed_at', 'desc')
        ->take($limit)
        ->get();
    }

    /**
     * Get top rated products
     */
    private function getTopRatedProducts($limit)
    {
        return Product::withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->having('approved_reviews_count', '>=', 5)
            ->where('status', 'active')
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get new arrivals
     */
    private function getNewArrivals($limit)
    {
        return Product::where('status', 'active')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get best sellers
     */
    private function getBestSellers($limit)
    {
        return Product::withCount('orderItems')
            ->where('status', 'active')
            ->orderBy('order_items_count', 'desc')
            ->take($limit)
            ->get();
    }
}
