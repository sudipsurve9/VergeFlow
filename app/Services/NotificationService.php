<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send review reminder email after order delivery
     */
    public function sendReviewReminder(Order $order)
    {
        try {
            // Check if order is delivered and no reminder sent yet
            if ($order->status === 'delivered' && !$order->review_reminder_sent) {
                $user = $order->user;
                $orderItems = $order->orderItems()->with('product')->get();
                
                // Get products that haven't been reviewed yet
                $unreviewed_products = [];
                foreach ($orderItems as $item) {
                    $existing_review = ProductReview::where('user_id', $user->id)
                        ->where('product_id', $item->product_id)
                        ->where('order_id', $order->id)
                        ->first();
                    
                    if (!$existing_review) {
                        $unreviewed_products[] = $item->product;
                    }
                }
                
                if (!empty($unreviewed_products)) {
                    Mail::send('emails.review-reminder', [
                        'user' => $user,
                        'order' => $order,
                        'products' => $unreviewed_products
                    ], function ($message) use ($user) {
                        $message->to($user->email, $user->name)
                                ->subject('How was your recent purchase? Share your experience!');
                    });
                    
                    // Mark reminder as sent
                    $order->update(['review_reminder_sent' => true]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send review reminder: ' . $e->getMessage());
        }
    }

    /**
     * Send notification when review is marked helpful
     */
    public function sendHelpfulVoteNotification(ProductReview $review, User $voter)
    {
        try {
            if ($review->user_id !== $voter->id) {
                Mail::send('emails.helpful-vote', [
                    'reviewer' => $review->user,
                    'voter' => $voter,
                    'review' => $review,
                    'product' => $review->product
                ], function ($message) use ($review) {
                    $message->to($review->user->email, $review->user->name)
                            ->subject('Someone found your review helpful!');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send helpful vote notification: ' . $e->getMessage());
        }
    }

    /**
     * Send admin notification for new review
     */
    public function sendAdminReviewNotification(ProductReview $review)
    {
        try {
            $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();
            
            foreach ($admins as $admin) {
                Mail::send('emails.admin-new-review', [
                    'admin' => $admin,
                    'review' => $review,
                    'product' => $review->product,
                    'customer' => $review->user
                ], function ($message) use ($admin) {
                    $message->to($admin->email, $admin->name)
                            ->subject('New Product Review Requires Approval');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send admin review notification: ' . $e->getMessage());
        }
    }

    /**
     * Send weekly review digest to customers
     */
    public function sendWeeklyReviewDigest(User $user)
    {
        try {
            // Get user's reviews from the past week
            $recent_reviews = ProductReview::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subWeek())
                ->with('product')
                ->get();
            
            // Get helpful votes received this week
            $helpful_votes = ProductReview::where('user_id', $user->id)
                ->where('updated_at', '>=', now()->subWeek())
                ->where('helpful_count', '>', 0)
                ->with('product')
                ->get();
            
            // Get recommended products based on purchase history
            $recommended_products = $this->getRecommendedProducts($user, 5);
            
            if ($recent_reviews->count() > 0 || $helpful_votes->count() > 0 || $recommended_products->count() > 0) {
                Mail::send('emails.weekly-digest', [
                    'user' => $user,
                    'recent_reviews' => $recent_reviews,
                    'helpful_votes' => $helpful_votes,
                    'recommended_products' => $recommended_products
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                            ->subject('Your Weekly Review Activity & Recommendations');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send weekly digest: ' . $e->getMessage());
        }
    }

    /**
     * Send order status update notifications
     */
    public function sendOrderStatusNotification(Order $order, $previous_status)
    {
        try {
            $user = $order->user;
            $status_messages = [
                'processing' => 'Your order is being processed',
                'shipped' => 'Your order has been shipped',
                'delivered' => 'Your order has been delivered',
                'cancelled' => 'Your order has been cancelled'
            ];
            
            if (isset($status_messages[$order->status])) {
                Mail::send('emails.order-status', [
                    'user' => $user,
                    'order' => $order,
                    'status_message' => $status_messages[$order->status],
                    'previous_status' => $previous_status
                ], function ($message) use ($user, $order) {
                    $message->to($user->email, $user->name)
                            ->subject('Order #' . $order->id . ' Status Update');
                });
                
                // Send review reminder if delivered
                if ($order->status === 'delivered') {
                    // Schedule review reminder for 3 days later
                    $this->scheduleReviewReminder($order, now()->addDays(3));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send order status notification: ' . $e->getMessage());
        }
    }

    /**
     * Schedule review reminder (would integrate with queue system)
     */
    private function scheduleReviewReminder(Order $order, $send_at)
    {
        // In a real implementation, this would use Laravel's job queue
        // For now, we'll just log it
        Log::info("Review reminder scheduled for order {$order->id} at {$send_at}");
    }

    /**
     * Get recommended products for user
     */
    private function getRecommendedProducts(User $user, $limit = 5)
    {
        // Get products from categories user has purchased from
        $purchased_categories = $user->orders()
            ->with('orderItems.product.category')
            ->get()
            ->pluck('orderItems.*.product.category.id')
            ->flatten()
            ->unique();
        
        if ($purchased_categories->isEmpty()) {
            // If no purchase history, return popular products
            return Product::withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->orderBy('approved_reviews_avg_rating', 'desc')
                ->take($limit)
                ->get();
        }
        
        // Get highly rated products from purchased categories
        return Product::whereIn('category_id', $purchased_categories)
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->having('approved_reviews_count', '>', 0)
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Send product back in stock notification
     */
    public function sendBackInStockNotification(Product $product, array $users)
    {
        try {
            foreach ($users as $user) {
                Mail::send('emails.back-in-stock', [
                    'user' => $user,
                    'product' => $product
                ], function ($message) use ($user, $product) {
                    $message->to($user->email, $user->name)
                            ->subject($product->name . ' is back in stock!');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send back in stock notification: ' . $e->getMessage());
        }
    }

    /**
     * Send price drop notification
     */
    public function sendPriceDropNotification(Product $product, $old_price, array $users)
    {
        try {
            $discount_percentage = round((($old_price - $product->price) / $old_price) * 100);
            
            foreach ($users as $user) {
                Mail::send('emails.price-drop', [
                    'user' => $user,
                    'product' => $product,
                    'old_price' => $old_price,
                    'discount_percentage' => $discount_percentage
                ], function ($message) use ($user, $product, $discount_percentage) {
                    $message->to($user->email, $user->name)
                            ->subject($product->name . ' - ' . $discount_percentage . '% Price Drop!');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send price drop notification: ' . $e->getMessage());
        }
    }
}
