<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of reviews with analytics
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['user', 'product', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->status) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        // Filter by rating
        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        // Search in review content
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('review', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('product', function($productQuery) use ($request) {
                      $productQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $reviews = $query->paginate(20);

        // Analytics data
        $analytics = [
            'total_reviews' => ProductReview::count(),
            'pending_reviews' => ProductReview::where('is_approved', false)->count(),
            'approved_reviews' => ProductReview::where('is_approved', true)->count(),
            'average_rating' => ProductReview::where('is_approved', true)->avg('rating'),
            'reviews_this_month' => ProductReview::whereMonth('created_at', now()->month)->count(),
            'verified_purchases' => ProductReview::where('is_verified_purchase', true)->count(),
        ];

        // Rating breakdown
        $ratingBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingBreakdown[$i] = ProductReview::where('rating', $i)
                ->where('is_approved', true)
                ->count();
        }

        // Top reviewed products
        $topProducts = Product::withCount(['approvedReviews'])
            ->orderBy('approved_reviews_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reviews.index', compact(
            'reviews', 
            'analytics', 
            'ratingBreakdown', 
            'topProducts'
        ));
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        $review = ProductReview::with(['user', 'product', 'order'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review
     */
    public function approve($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update(['is_approved' => true]);
        
        return back()->with('success', 'Review approved successfully.');
    }

    /**
     * Reject a review
     */
    public function reject($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->update(['is_approved' => false]);
        
        return back()->with('success', 'Review rejected successfully.');
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $request->review_ids)
            ->update(['is_approved' => true]);

        return back()->with('success', count($request->review_ids) . ' reviews approved successfully.');
    }

    /**
     * Bulk reject reviews
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $request->review_ids)
            ->update(['is_approved' => false]);

        return back()->with('success', count($request->review_ids) . ' reviews rejected successfully.');
    }

    /**
     * Delete a review
     */
    public function destroy($id)
    {
        $review = ProductReview::findOrFail($id);
        $review->delete();
        
        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Bulk delete reviews
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:product_reviews,id'
        ]);

        ProductReview::whereIn('id', $request->review_ids)->delete();

        return back()->with('success', count($request->review_ids) . ' reviews deleted successfully.');
    }

    /**
     * Export reviews to CSV
     */
    public function export(Request $request)
    {
        $query = ProductReview::with(['user', 'product'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->status === 'approved') {
            $query->where('is_approved', true);
        }

        if ($request->rating) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->get();

        $filename = 'reviews_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Product', 'Customer', 'Rating', 'Title', 'Review', 
                'Verified Purchase', 'Approved', 'Helpful Count', 'Created At'
            ]);

            // CSV data
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->product->name,
                    $review->user->name,
                    $review->rating,
                    $review->title,
                    $review->review,
                    $review->is_verified_purchase ? 'Yes' : 'No',
                    $review->is_approved ? 'Yes' : 'No',
                    $review->helpful_count,
                    $review->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Analytics dashboard
     */
    public function analytics()
    {
        // Monthly review trends
        $monthlyTrends = ProductReview::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(rating) as avg_rating')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Product performance
        $productPerformance = Product::select('products.*')
            ->withCount(['approvedReviews'])
            ->withAvg(['approvedReviews'], 'rating')
            ->having('approved_reviews_count', '>', 0)
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take(20)
            ->get();

        // Customer engagement
        $customerEngagement = User::select('users.*')
            ->withCount(['reviews'])
            ->having('reviews_count', '>', 0)
            ->orderBy('reviews_count', 'desc')
            ->take(20)
            ->get();

        return view('admin.reviews.analytics', compact(
            'monthlyTrends',
            'productPerformance', 
            'customerEngagement'
        ));
    }
}
