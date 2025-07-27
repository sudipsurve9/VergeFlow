<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\RecentlyViewed;
use App\Models\Wishlist;
use App\Services\RecommendationService;
use App\Services\NotificationService;
use App\Services\SearchService;
use App\Services\SocialFeaturesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AmazonStyleFeaturesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $admin;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create(['category_id' => $this->category->id]);
        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function user_can_create_product_review()
    {
        // Create an order for verified purchase
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id
        ]);

        $this->actingAs($this->user);

        $reviewData = [
            'rating' => 5,
            'title' => 'Excellent product!',
            'review' => 'This product exceeded my expectations. Highly recommended!',
        ];

        $response = $this->post(route('products.reviews.store', $this->product), $reviewData);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Excellent product!',
            'is_verified_purchase' => true
        ]);
    }

    /** @test */
    public function user_can_edit_their_review()
    {
        $review = ProductReview::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        $this->actingAs($this->user);

        $updatedData = [
            'rating' => 4,
            'title' => 'Updated review title',
            'review' => 'Updated review content'
        ];

        $response = $this->put(route('reviews.update', $review), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'rating' => 4,
            'title' => 'Updated review title'
        ]);
    }

    /** @test */
    public function user_can_mark_review_as_helpful()
    {
        $review = ProductReview::factory()->create([
            'product_id' => $this->product->id,
            'helpful_count' => 0
        ]);

        $this->actingAs($this->user);

        $response = $this->post(route('reviews.helpful', $review));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'helpful_count' => 1
        ]);
    }

    /** @test */
    public function recently_viewed_products_are_tracked()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('products.show', $this->product));

        $response->assertStatus(200);
        $this->assertDatabaseHas('recently_viewed', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /** @test */
    public function admin_can_approve_reviews()
    {
        $review = ProductReview::factory()->create([
            'product_id' => $this->product->id,
            'is_approved' => false
        ]);

        $this->actingAs($this->admin);

        $response = $this->put(route('admin.reviews.approve', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'is_approved' => true
        ]);
    }

    /** @test */
    public function admin_can_bulk_approve_reviews()
    {
        $reviews = ProductReview::factory()->count(3)->create([
            'product_id' => $this->product->id,
            'is_approved' => false
        ]);

        $this->actingAs($this->admin);

        $response = $this->post(route('admin.reviews.bulk-approve'), [
            'review_ids' => $reviews->pluck('id')->toArray()
        ]);

        $response->assertRedirect();
        foreach ($reviews as $review) {
            $this->assertDatabaseHas('product_reviews', [
                'id' => $review->id,
                'is_approved' => true
            ]);
        }
    }

    /** @test */
    public function recommendation_service_returns_related_products()
    {
        // Create additional products in same category
        $relatedProducts = Product::factory()->count(5)->create([
            'category_id' => $this->category->id
        ]);

        $recommendationService = new RecommendationService();
        $recommendations = $recommendationService->getCustomersAlsoBought($this->product, 4);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $recommendations);
        $this->assertLessThanOrEqual(4, $recommendations->count());
    }

    /** @test */
    public function search_service_filters_by_rating()
    {
        // Create products with reviews
        $highRatedProduct = Product::factory()->create(['category_id' => $this->category->id]);
        $lowRatedProduct = Product::factory()->create(['category_id' => $this->category->id]);

        ProductReview::factory()->create([
            'product_id' => $highRatedProduct->id,
            'rating' => 5,
            'is_approved' => true
        ]);

        ProductReview::factory()->create([
            'product_id' => $lowRatedProduct->id,
            'rating' => 2,
            'is_approved' => true
        ]);

        $searchService = new SearchService();
        $request = new \Illuminate\Http\Request(['min_rating' => 4]);
        
        $results = $searchService->searchProducts($request)->get();

        $this->assertTrue($results->contains('id', $highRatedProduct->id));
        $this->assertFalse($results->contains('id', $lowRatedProduct->id));
    }

    /** @test */
    public function social_features_service_generates_reviewer_badges()
    {
        // Create multiple reviews for user
        ProductReview::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'is_approved' => true,
            'helpful_count' => 5
        ]);

        $socialService = new SocialFeaturesService();
        $badges = $socialService->getReviewerBadges($this->user);

        $this->assertIsArray($badges);
        $this->assertNotEmpty($badges);
        
        $badgeNames = collect($badges)->pluck('name')->toArray();
        $this->assertContains('Active Reviewer', $badgeNames);
    }

    /** @test */
    public function product_rating_calculations_are_accurate()
    {
        // Create reviews with different ratings
        ProductReview::factory()->create([
            'product_id' => $this->product->id,
            'rating' => 5,
            'is_approved' => true
        ]);
        ProductReview::factory()->create([
            'product_id' => $this->product->id,
            'rating' => 4,
            'is_approved' => true
        ]);
        ProductReview::factory()->create([
            'product_id' => $this->product->id,
            'rating' => 3,
            'is_approved' => true
        ]);

        $this->product->refresh();

        $this->assertEquals(4.0, $this->product->averageRating());
        $this->assertEquals(3, $this->product->totalReviews());
        $this->assertEquals(4, $this->product->starRating());
    }

    /** @test */
    public function wishlist_functionality_works()
    {
        $this->actingAs($this->user);

        // Add to wishlist
        $response = $this->post(route('wishlists.store'), [
            'product_id' => $this->product->id
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);

        // Remove from wishlist
        $wishlistItem = Wishlist::where('user_id', $this->user->id)
            ->where('product_id', $this->product->id)
            ->first();

        $response = $this->delete(route('wishlists.destroy', $wishlistItem->id));

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    /** @test */
    public function review_images_can_be_uploaded()
    {
        Storage::fake('public');

        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('review.jpg');

        $reviewData = [
            'rating' => 5,
            'title' => 'Great product with photos',
            'review' => 'Here are some photos of the product',
            'images' => [$file]
        ];

        $response = $this->post(route('products.reviews.store', $this->product), $reviewData);

        $response->assertRedirect();
        
        $review = ProductReview::where('user_id', $this->user->id)
            ->where('product_id', $this->product->id)
            ->first();

        $this->assertNotNull($review);
        $this->assertNotNull($review->images);
        $this->assertIsArray($review->images);
    }

    /** @test */
    public function admin_can_export_reviews()
    {
        ProductReview::factory()->count(5)->create([
            'product_id' => $this->product->id,
            'is_approved' => true
        ]);

        $this->actingAs($this->admin);

        $response = $this->get(route('admin.reviews.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function guest_recently_viewed_uses_session()
    {
        $response = $this->get(route('products.show', $this->product));

        $response->assertStatus(200);
        
        // Should create session-based recently viewed record
        $this->assertDatabaseHas('recently_viewed', [
            'product_id' => $this->product->id,
            'user_id' => null
        ]);
    }
}
