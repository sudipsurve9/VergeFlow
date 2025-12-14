<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\OrderStatusHistory;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        // Debug database connection
        $user = auth()->user();
        $clientId = $user->client_id;
        $defaultConnection = config('database.default');
        
        \Log::info('Dashboard Debug', [
            'user_id' => $user->id,
            'client_id' => $clientId,
            'default_connection' => $defaultConnection,
            'session_client_id' => session('current_client_id'),
        ]);
        
        // Use client database directly
        $connection = 'client';
        
        // Test database connection first
        try {
            $testConnection = \DB::connection($connection)->getPdo();
            \Log::info('Database connection successful', ['connection' => $connection]);
        } catch (\Exception $e) {
            \Log::error('Database connection failed: ' . $e->getMessage());
        }
        
        try {
            // Get raw counts with detailed logging
            $totalOrders = \DB::connection($connection)->table('orders')->count();
            $totalProducts = \DB::connection($connection)->table('products')->count();
            $totalUsers = \DB::connection($connection)->table('users')->where('role', 'user')->count();
            $totalRevenue = \DB::connection($connection)->table('orders')->where('payment_status', 'paid')->sum('total_amount') ?? 0;
            $pendingOrders = \DB::connection($connection)->table('orders')->where('status', 'pending')->count();
            $lowStockProducts = \DB::connection($connection)->table('products')->where('stock_quantity', '<', 10)->count();
            
            \Log::info('Dashboard raw queries', [
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'low_stock_products' => $lowStockProducts,
            ]);
            
            // Get additional counts for Module Overview
            $totalCategories = \DB::connection($connection)->table('categories')->count();
            $totalReviews = 0; // No reviews table yet
            $totalCoupons = 0; // No coupons table yet
            
            // System status - check API integrations (Stripe, Shiprocket, etc.)
            $activeApiIntegrations = 0;
            if (config('services.stripe.key')) $activeApiIntegrations++;
            if (config('shiprocket.api_key')) $activeApiIntegrations++;
            
            $stats = [
                'total_orders' => $totalOrders,
                'total_products' => $totalProducts,
                'total_users' => $totalUsers,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'low_stock_products' => $lowStockProducts,
                'total_categories' => $totalCategories,
                'total_reviews' => $totalReviews,
                'total_coupons' => $totalCoupons,
                'active_api_integrations' => $activeApiIntegrations,
            ];
        } catch (\Exception $e) {
            \Log::error('Dashboard query error: ' . $e->getMessage());
            \Log::error('Dashboard query stack trace: ' . $e->getTraceAsString());
            // Fallback to empty stats
            $stats = [
                'total_orders' => 0,
                'total_products' => 0,
                'total_users' => 0,
                'total_revenue' => 0,
                'pending_orders' => 0,
                'low_stock_products' => 0,
                'total_categories' => 0,
                'total_reviews' => 0,
                'total_coupons' => 0,
                'active_api_integrations' => 0,
            ];
        }

        try {
            $recent_orders = \DB::connection($connection)->table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->select('orders.*', 'users.name as user_name')
                ->orderBy('orders.created_at', 'desc')
                ->limit(5)
                ->get();
            $top_products = \DB::connection($connection)->table('products')->limit(5)->get();
        } catch (\Exception $e) {
            \Log::error('Dashboard additional queries error: ' . $e->getMessage());
            $recent_orders = collect();
            $top_products = collect();
        }

        \Log::info('Final dashboard stats', $stats);
        return view('admin.dashboard', compact('stats', 'recent_orders', 'top_products'));
    }

    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = Order::with(['user', 'orderItems.product'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);
        if ($oldStatus !== $request->status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $request->status,
                'comment' => 'Order status updated by admin',
            ]);
        }
        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function updateOrderTracking(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $oldDeliveryStatus = $order->delivery_status;
        $request->validate([
            'delivery_status' => 'required|string',
            'tracking_number' => 'nullable|string',
            'courier_name' => 'nullable|string',
            'courier_url' => 'nullable|url',
        ]);
        $order->update([
            'delivery_status' => $request->delivery_status,
            'tracking_number' => $request->tracking_number,
            'courier_name' => $request->courier_name,
            'courier_url' => $request->courier_url,
        ]);
        if ($oldDeliveryStatus !== $request->delivery_status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $request->delivery_status,
                'comment' => 'Delivery status updated by admin',
            ]);
        }
        return redirect()->back()->with('success', 'Delivery tracking updated successfully');
    }

    public function products()
    {
        $products = Product::with('category')->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function categories()
    {
        $categories = Category::withCount('products')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // Category Management Methods
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category with existing products');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }

    // User Management Methods
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin'
        ]);

        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
