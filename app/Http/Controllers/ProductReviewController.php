<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\Order;

class ProductReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display reviews for a product
     */
    public function index(Product $product)
    {
        $reviews = ProductReview::with('user')
            ->where('product_id', $product->id)
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->total();
        
        // Rating breakdown
        $ratingBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingBreakdown[$i] = ProductReview::where('product_id', $product->id)
                ->approved()
                ->byRating($i)
                ->count();
        }

        return view('products.reviews', compact('product', 'reviews', 'averageRating', 'totalReviews', 'ratingBreakdown'));
    }

    /**
     * Show form to create a review
     */
    public function create(Product $product)
    {
        // Check if user has purchased this product
        $hasPurchased = Order::where('user_id', Auth::id())
            ->whereHas('items', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->exists();

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('products.show', $product->slug)
                ->with('error', 'You have already reviewed this product.');
        }

        return view('products.review-form', compact('product', 'hasPurchased'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:10|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('products.show', $product->slug)
                ->with('error', 'You have already reviewed this product.');
        }

        // Check if user has purchased this product
        $order = Order::where('user_id', Auth::id())
            ->whereHas('items', function($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->first();

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = $path;
            }
        }

        ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'order_id' => $order ? $order->id : null,
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'is_verified_purchase' => $order ? true : false,
            'is_approved' => true, // Auto-approve for now
            'images' => $images
        ]);

        return redirect()->route('products.show', $product->slug)
            ->with('success', 'Thank you for your review! It has been submitted successfully.');
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(ProductReview $review)
    {
        $review->increment('helpful_count');
        
        if (request()->ajax()) {
            return response()->json([
                'status' => 'success',
                'helpful_count' => $review->helpful_count
            ]);
        }

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * User's reviews
     */
    public function myReviews()
    {
        $reviews = ProductReview::with('product')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('profile.reviews', compact('reviews'));
    }

    /**
     * Edit user's review
     */
    public function edit(ProductReview $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        return view('products.edit-review', compact('review'));
    }

    /**
     * Update user's review
     */
    public function update(Request $request, ProductReview $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'review' => 'required|string|min:10|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $images = $review->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $images[] = $path;
            }
        }

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'review' => $request->review,
            'images' => $images,
            'is_approved' => true // Re-approve after edit
        ]);

        return redirect()->route('products.show', $review->product->slug)
            ->with('success', 'Your review has been updated successfully.');
    }

    /**
     * Delete user's review
     */
    public function destroy(ProductReview $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Your review has been deleted.');
    }
}
