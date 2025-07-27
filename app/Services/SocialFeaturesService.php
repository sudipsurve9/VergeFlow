<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SocialFeaturesService
{
    /**
     * Generate shareable review content
     */
    public function generateShareableReview(ProductReview $review)
    {
        $product = $review->product;
        $stars = str_repeat('⭐', $review->rating);
        
        $content = [
            'text' => "I just reviewed \"{$product->name}\" - {$stars} ({$review->rating}/5)\n\n\"{$review->title}\"\n\nCheck it out on VergeFlow!",
            'url' => route('products.show', $product),
            'image' => $product->image ? Storage::url($product->image) : null,
            'hashtags' => ['VergeFlow', 'ProductReview', str_replace(' ', '', $product->category->name ?? 'Shopping')]
        ];

        return $content;
    }

    /**
     * Get reviewer badges for user
     */
    public function getReviewerBadges(User $user)
    {
        $badges = [];
        $reviewCount = $user->reviews()->where('is_approved', true)->count();
        $helpfulVotes = $user->reviews()->sum('helpful_count');
        $verifiedReviews = $user->reviews()->where('is_verified_purchase', true)->count();

        // Review count badges
        if ($reviewCount >= 100) {
            $badges[] = ['name' => 'Expert Reviewer', 'icon' => '🏆', 'description' => '100+ reviews'];
        } elseif ($reviewCount >= 50) {
            $badges[] = ['name' => 'Prolific Reviewer', 'icon' => '🌟', 'description' => '50+ reviews'];
        } elseif ($reviewCount >= 10) {
            $badges[] = ['name' => 'Active Reviewer', 'icon' => '📝', 'description' => '10+ reviews'];
        } elseif ($reviewCount >= 1) {
            $badges[] = ['name' => 'First Review', 'icon' => '🎯', 'description' => 'Posted first review'];
        }

        // Helpful votes badges
        if ($helpfulVotes >= 500) {
            $badges[] = ['name' => 'Community Hero', 'icon' => '🦸', 'description' => '500+ helpful votes'];
        } elseif ($helpfulVotes >= 100) {
            $badges[] = ['name' => 'Helpful Reviewer', 'icon' => '👍', 'description' => '100+ helpful votes'];
        } elseif ($helpfulVotes >= 25) {
            $badges[] = ['name' => 'Trusted Voice', 'icon' => '✅', 'description' => '25+ helpful votes'];
        }

        // Verified purchase badges
        if ($verifiedReviews >= 20) {
            $badges[] = ['name' => 'Verified Buyer', 'icon' => '🛒', 'description' => '20+ verified purchases'];
        }

        // Special badges
        $avgRating = $user->reviews()->where('is_approved', true)->avg('rating');
        if ($avgRating && $avgRating >= 4.5 && $reviewCount >= 10) {
            $badges[] = ['name' => 'Quality Focused', 'icon' => '💎', 'description' => 'High average ratings'];
        }

        return $badges;
    }

    /**
     * Get top reviewers leaderboard
     */
    public function getTopReviewers($limit = 10, $period = 'all_time')
    {
        $query = User::withCount(['reviews' => function($q) use ($period) {
            $q->where('is_approved', true);
            if ($period === 'month') {
                $q->whereMonth('created_at', now()->month);
            } elseif ($period === 'year') {
                $q->whereYear('created_at', now()->year);
            }
        }])
        ->withSum(['reviews' => function($q) use ($period) {
            $q->where('is_approved', true);
            if ($period === 'month') {
                $q->whereMonth('created_at', now()->month);
            } elseif ($period === 'year') {
                $q->whereYear('created_at', now()->year);
            }
        }], 'helpful_count')
        ->having('reviews_count', '>', 0)
        ->orderBy('reviews_sum_helpful_count', 'desc')
        ->orderBy('reviews_count', 'desc');

        return $query->take($limit)->get()->map(function($user) {
            $user->badges = $this->getReviewerBadges($user);
            return $user;
        });
    }

    /**
     * Generate review contest entries
     */
    public function getReviewContestEntries($contestId = null)
    {
        // Monthly review contest
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        $entries = ProductReview::with(['user', 'product'])
            ->where('is_approved', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('helpful_count', '>=', 5) // Minimum helpful votes
            ->whereNotNull('images') // Must have images
            ->orderBy('helpful_count', 'desc')
            ->orderBy('rating', 'desc')
            ->take(20)
            ->get();

        return $entries;
    }

    /**
     * Follow/Unfollow reviewer functionality
     */
    public function followReviewer(User $follower, User $reviewer)
    {
        // Create follower relationship (you'd need to create a followers table)
        DB::table('reviewer_followers')->updateOrInsert([
            'follower_id' => $follower->id,
            'reviewer_id' => $reviewer->id
        ], [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return true;
    }

    public function unfollowReviewer(User $follower, User $reviewer)
    {
        DB::table('reviewer_followers')
            ->where('follower_id', $follower->id)
            ->where('reviewer_id', $reviewer->id)
            ->delete();

        return true;
    }

    /**
     * Get reviews from followed reviewers
     */
    public function getFollowedReviewersActivity(User $user, $limit = 20)
    {
        $followedReviewerIds = DB::table('reviewer_followers')
            ->where('follower_id', $user->id)
            ->pluck('reviewer_id');

        if ($followedReviewerIds->isEmpty()) {
            return collect();
        }

        return ProductReview::with(['user', 'product'])
            ->whereIn('user_id', $followedReviewerIds)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Generate review gallery for product
     */
    public function getReviewGallery(Product $product, $limit = 20)
    {
        $reviews = ProductReview::where('product_id', $product->id)
            ->where('is_approved', true)
            ->whereNotNull('images')
            ->orderBy('helpful_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $gallery = [];
        foreach ($reviews as $review) {
            if ($review->images) {
                foreach ($review->images as $image) {
                    $gallery[] = [
                        'image' => Storage::url($image),
                        'review' => $review,
                        'user' => $review->user,
                        'rating' => $review->rating,
                        'title' => $review->title
                    ];
                }
            }
        }

        return collect($gallery);
    }

    /**
     * Get review statistics for gamification
     */
    public function getReviewStatistics(User $user)
    {
        $stats = [
            'total_reviews' => $user->reviews()->where('is_approved', true)->count(),
            'total_helpful_votes' => $user->reviews()->sum('helpful_count'),
            'average_rating_given' => round($user->reviews()->where('is_approved', true)->avg('rating'), 1),
            'verified_purchases' => $user->reviews()->where('is_verified_purchase', true)->count(),
            'reviews_with_images' => $user->reviews()->whereNotNull('images')->count(),
            'this_month_reviews' => $user->reviews()->whereMonth('created_at', now()->month)->count(),
            'this_year_reviews' => $user->reviews()->whereYear('created_at', now()->year)->count(),
        ];

        // Calculate review streak
        $stats['current_streak'] = $this->calculateReviewStreak($user);
        
        // Get next badge progress
        $stats['next_badge'] = $this->getNextBadgeProgress($user);

        return $stats;
    }

    /**
     * Calculate review streak (consecutive months with reviews)
     */
    private function calculateReviewStreak(User $user)
    {
        $streak = 0;
        $currentMonth = now();

        for ($i = 0; $i < 12; $i++) {
            $hasReview = $user->reviews()
                ->where('is_approved', true)
                ->whereYear('created_at', $currentMonth->year)
                ->whereMonth('created_at', $currentMonth->month)
                ->exists();

            if ($hasReview) {
                $streak++;
                $currentMonth = $currentMonth->subMonth();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get progress towards next badge
     */
    private function getNextBadgeProgress(User $user)
    {
        $reviewCount = $user->reviews()->where('is_approved', true)->count();
        $helpfulVotes = $user->reviews()->sum('helpful_count');

        $nextBadges = [];

        // Review count progression
        if ($reviewCount < 10) {
            $nextBadges[] = [
                'name' => 'Active Reviewer',
                'progress' => $reviewCount,
                'target' => 10,
                'type' => 'reviews'
            ];
        } elseif ($reviewCount < 50) {
            $nextBadges[] = [
                'name' => 'Prolific Reviewer',
                'progress' => $reviewCount,
                'target' => 50,
                'type' => 'reviews'
            ];
        } elseif ($reviewCount < 100) {
            $nextBadges[] = [
                'name' => 'Expert Reviewer',
                'progress' => $reviewCount,
                'target' => 100,
                'type' => 'reviews'
            ];
        }

        // Helpful votes progression
        if ($helpfulVotes < 25) {
            $nextBadges[] = [
                'name' => 'Trusted Voice',
                'progress' => $helpfulVotes,
                'target' => 25,
                'type' => 'helpful_votes'
            ];
        } elseif ($helpfulVotes < 100) {
            $nextBadges[] = [
                'name' => 'Helpful Reviewer',
                'progress' => $helpfulVotes,
                'target' => 100,
                'type' => 'helpful_votes'
            ];
        }

        return $nextBadges;
    }

    /**
     * Generate social sharing URLs
     */
    public function getSocialSharingUrls(ProductReview $review)
    {
        $shareContent = $this->generateShareableReview($review);
        $encodedText = urlencode($shareContent['text']);
        $encodedUrl = urlencode($shareContent['url']);
        $hashtags = implode(',', $shareContent['hashtags']);

        return [
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'twitter' => "https://twitter.com/intent/tweet?text={$encodedText}&url={$encodedUrl}&hashtags={$hashtags}",
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'whatsapp' => "https://wa.me/?text={$encodedText}%20{$encodedUrl}",
            'email' => "mailto:?subject=" . urlencode("Check out this product review") . "&body={$encodedText}%20{$encodedUrl}"
        ];
    }
}
