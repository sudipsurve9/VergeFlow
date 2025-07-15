@extends('layouts.admin')

@section('title', 'Customer: ' . $customer->first_name . ' ' . $customer->last_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Customer Profile</h3>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Customers
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->first_name . ' ' . $customer->last_name) }}&background=FFB300&color=fff&size=96" class="rounded-circle mb-3" width="96" height="96" alt="Avatar">
                            <h4>{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                            <p class="text-muted mb-1">{{ $customer->user->email }}</p>
                            @if($customer->phone)
                                <p class="text-muted mb-1">{{ $customer->phone }}</p>
                            @endif
                            <span class="badge badge-{{ $customer->is_active ? 'success' : 'danger' }} mb-2">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
                            <div class="mb-2">
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-warning mb-1"><i class="fas fa-edit"></i> Edit</a>
                                <form action="{{ route('admin.customers.toggle-status', $customer) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm {{ $customer->is_active ? 'btn-secondary' : 'btn-success' }} mb-1">
                                        <i class="fas {{ $customer->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i> {{ $customer->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.customers.reset-password', $customer) }}" method="POST" style="display:inline;" onsubmit="return confirm('Reset password to default (password123)?')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-warning mb-1"><i class="fas fa-key"></i> Reset Password</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Date of Birth:</strong><br>
                                    {{ $customer->date_of_birth ? $customer->date_of_birth->format('M d, Y') : '-' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Gender:</strong><br>
                                    {{ ucfirst($customer->gender) ?: '-' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Joined:</strong><br>
                                    {{ $customer->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>Notes:</strong><br>
                                {{ $customer->notes ?: '-' }}
                            </div>
                            <div class="mb-3">
                                <strong>Addresses:</strong><br>
                                @if($customer->addresses->count())
                                    <ul class="list-unstyled">
                                        @foreach($customer->addresses as $address)
                                            <li>{{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No addresses found.</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Recent Orders:</strong><br>
                                @if($recentOrders->count())
                                    <ul class="list-unstyled">
                                        @foreach($recentOrders as $order)
                                            <li>
                                                <a href="{{ route('admin.orders.show', $order) }}">Order #{{ $order->id }}</a> - ₹{{ number_format($order->total_amount, 2) }} ({{ $order->created_at->format('M d, Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No recent orders.</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Total Spent:</strong> ₹{{ number_format($totalSpent, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 