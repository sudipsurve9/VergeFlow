<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_remove_item_from_cart()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a category and product
        $category = \App\Models\Category::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10, 'category_id' => $category->id]);

        // Add item to cart
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Assert item exists in cart
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'user_id' => $user->id,
        ]);

        // Remove item from cart
        $response = $this->delete(route('cart.remove', $cartItem->id));

        // Check if item is deleted
        $this->assertNull(CartItem::find($cartItem->id));

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item removed from cart');
    }

    /** @test */
    public function user_can_access_checkout_page()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $response = $this->actingAs($user)->get(route('orders.checkout'));
        $response->assertStatus(200);
        $response->assertSee('Checkout');
    }

    /** @test */
    public function user_can_submit_valid_checkout()
    {
        $user = User::factory()->create();
        $category = \App\Models\Category::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10, 'category_id' => $category->id]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $checkoutData = [
            'shipping_address' => '123 Test St',
            'billing_address' => '123 Test St',
            'phone' => '1234567890',
            'payment_method' => 'cod',
        ];
        $response = $this->actingAs($user)->post(route('checkout.process'), $checkoutData);
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'shipping_address' => '123 Test St',
        ]);
    }

    /** @test */
    public function checkout_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('checkout.process'), []);
        $response->assertSessionHasErrors(['shipping_address', 'billing_address', 'phone', 'payment_method']);
    }

    /** @test */
    public function user_can_apply_valid_coupon()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
        ]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $response = $this->actingAs($user)->post(route('cart.applyCoupon'), [
            'coupon_code' => 'SAVE10',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('coupon_usages', [
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
        ]);
    }

    /** @test */
    public function user_cannot_apply_invalid_coupon()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $response = $this->actingAs($user)->post(route('cart.applyCoupon'), [
            'coupon_code' => 'INVALIDCODE',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function user_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $response = $this->actingAs($user)->post(route('wishlists.store'), [
            'product_id' => $product->id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    /** @test */
    public function user_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $response = $this->actingAs($user)->delete(route('wishlists.destroy', $wishlist->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('wishlists', [
            'id' => $wishlist->id,
        ]);
    }
}
