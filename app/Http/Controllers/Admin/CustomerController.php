<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with(['user', 'orders'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Create user account
        $user = \App\Models\User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
        ]);

        // Create customer profile
        $customer = Customer::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'is_active' => $request->has('is_active'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully. Default password: password123');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'user', 
            'orders.items.product', 
            'addresses',
            'reviews.product'
        ]);
        
        $recentOrders = $customer->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $totalSpent = $customer->orders()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
            
        return view('admin.customers.show', compact('customer', 'recentOrders', 'totalSpent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->user_id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Update user account
        $customer->user->update([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);

        // Update customer profile
        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'is_active' => $request->has('is_active'),
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has orders
        if ($customer->orders()->count() > 0) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Cannot delete customer with associated orders.');
        }

        // Delete addresses
        $customer->addresses()->delete();
        
        // Delete customer profile
        $customer->delete();
        
        // Delete user account
        $customer->user->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Toggle customer status
     */
    public function toggleStatus(Customer $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);
        
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer status updated successfully.');
    }

    /**
     * Reset customer password
     */
    public function resetPassword(Customer $customer)
    {
        $newPassword = 'password123';
        $customer->user->update(['password' => Hash::make($newPassword)]);
        
        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Password reset successfully. New password: ' . $newPassword);
    }

    /**
     * Customer orders
     */
    public function orders(Customer $customer)
    {
        $orders = $customer->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.customers.orders', compact('customer', 'orders'));
    }

    /**
     * Customer addresses
     */
    public function addresses(Customer $customer)
    {
        $addresses = $customer->addresses()->paginate(15);
        return view('admin.customers.addresses', compact('customer', 'addresses'));
    }

    /**
     * Customer reviews
     */
    public function reviews(Customer $customer)
    {
        $reviews = $customer->reviews()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.customers.reviews', compact('customer', 'reviews'));
    }

    /**
     * Export customers
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:active,inactive',
        ]);

        $query = Customer::with(['user', 'orders']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->orderBy('created_at', 'desc')->get();

        // TODO: Implement export logic based on format
        return redirect()->route('admin.customers.index')
            ->with('info', 'Export feature will be implemented soon.');
    }
}
