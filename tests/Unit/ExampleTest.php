<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100,
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 100,
        ]);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 250,
        ]);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 250,
        ]);
    }

    /** @test */
    public function it_can_create_a_coupon()
    {
        $coupon = Coupon::factory()->create([
            'code' => 'UNITTEST',
            'type' => 'fixed',
            'value' => 50,
        ]);
        $this->assertDatabaseHas('coupons', [
            'code' => 'UNITTEST',
            'type' => 'fixed',
            'value' => 50,
        ]);
    }

    /** @test */
    public function product_has_orders_relationship()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
        ]);
        $this->assertTrue($order->items()->where('product_id', $product->id)->exists());
    }
}
