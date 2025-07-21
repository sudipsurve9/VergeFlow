<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

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
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test admin can view print invoice page for an order.
     *
     * @return void
     */
    public function test_admin_can_view_print_invoice_page()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $shippingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'shipping']);
        $billingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'billing']);
        $order = \App\Models\Order::factory()
            ->for($admin, 'user')
            ->has(\App\Models\OrderItem::factory()->count(1), 'items')
            ->create([
                'shipping_address' => $shippingAddress->id,
                'billing_address' => $billingAddress->id,
            ]);
        \App\Models\Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
        ]);
        $response = $this->actingAs($admin)->get(route('admin.orders.invoice', $order));
        $response->assertStatus(200);
        $response->assertSee('INVOICE');
        $response->assertSee('Order #:');
    }

    /**
     * Test admin can download invoice PDF for an order.
     *
     * @return void
     */
    public function test_admin_can_download_invoice_pdf()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $shippingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'shipping']);
        $billingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'billing']);
        $order = \App\Models\Order::factory()
            ->for($admin, 'user')
            ->has(\App\Models\OrderItem::factory()->count(1), 'items')
            ->create([
                'shipping_address' => $shippingAddress->id,
                'billing_address' => $billingAddress->id,
            ]);
        \App\Models\Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => $order->total_amount,
        ]);
        $response = $this->actingAs($admin)->get(route('admin.orders.invoice.pdf', $order));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    /**
     * Test admin can download TCPDF invoice PDF for an order.
     *
     * @return void
     */
    public function test_admin_can_download_tcpdf_invoice_pdf()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $shippingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'shipping']);
        $billingAddress = \App\Models\Address::factory()->create(['user_id' => $admin->id, 'type' => 'billing']);
        $order = \App\Models\Order::factory()
            ->for($admin, 'user')
            ->has(\App\Models\OrderItem::factory()->count(1), 'items')
            ->create([
                'shipping_address' => $shippingAddress->id,
                'billing_address' => $billingAddress->id,
            ]);
        \App\Models\Payment::factory()->create([
            'order_id' => $order->id,
        ]);
        $response = $this->actingAs($admin)->get(route('admin.orders.invoice.tcpdf', $order));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_can_update_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);
        $user->refresh();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_create_product()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = \App\Models\Category::factory()->create();
        $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Admin Product',
            'price' => 199,
            'stock_quantity' => 5,
            'category_id' => $category->id,
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Admin Product',
            'price' => 199,
        ]);
    }

    /** @test */
    public function admin_can_update_order_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $order = \App\Models\Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $this->actingAs($admin)->put("/admin/orders/{$order->id}", [
            'status' => 'completed',
        ]);
        $order->refresh();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }
}
