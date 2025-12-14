@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Product Details</h3>
                        <div>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Product
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h4>Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Name:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>SKU:</th>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $product->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>
                                        @if($product->category)
                                            <span class="badge badge-info">{{ $product->category->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">No Category</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Featured:</th>
                                    <td>
                                        @if($product->is_featured)
                                            <span class="badge badge-warning">Featured</span>
                                        @else
                                            <span class="badge badge-secondary">Regular</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4>Pricing & Inventory</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Price:</th>
                                    <td><strong>₹{{ number_format($product->price, 2) }}</strong></td>
                                </tr>
                                @if($product->compare_price)
                                <tr>
                                    <th>Compare Price:</th>
                                    <td><del>₹{{ number_format($product->compare_price, 2) }}</del></td>
                                </tr>
                                @endif
                                @if($product->cost_price)
                                <tr>
                                    <th>Cost Price:</th>
                                    <td>₹{{ number_format($product->cost_price, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Stock Quantity:</th>
                                    <td>
                                        @if($product->stock_quantity <= 0)
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @elseif($product->stock_quantity <= ($product->low_stock_threshold ?? 5))
                                            <span class="badge badge-warning">{{ $product->stock_quantity }}</span>
                                        @else
                                            <span class="badge badge-success">{{ $product->stock_quantity }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($product->low_stock_threshold)
                                <tr>
                                    <th>Low Stock Threshold:</th>
                                    <td>{{ $product->low_stock_threshold }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($product->images)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Product Images</h4>
                                <div class="row">
                                    @php
                                        $images = json_decode($product->images, true) ?? [];
                                    @endphp
                                    @foreach($images as $image)
                                        <div class="col-md-3 mb-3">
                                            <img src="{{ asset('storage/' . $image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 100%; height: auto;">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($product->weight || $product->dimensions)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Shipping Information</h4>
                                <table class="table table-bordered">
                                    @if($product->weight)
                                    <tr>
                                        <th width="200">Weight:</th>
                                        <td>{{ $product->weight }} kg</td>
                                    </tr>
                                    @endif
                                    @if($product->dimensions)
                                    <tr>
                                        <th>Dimensions:</th>
                                        <td>{{ $product->dimensions }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($product->meta_title || $product->meta_description || $product->meta_keywords)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>SEO Information</h4>
                                <table class="table table-bordered">
                                    @if($product->meta_title)
                                    <tr>
                                        <th width="200">Meta Title:</th>
                                        <td>{{ $product->meta_title }}</td>
                                    </tr>
                                    @endif
                                    @if($product->meta_description)
                                    <tr>
                                        <th>Meta Description:</th>
                                        <td>{{ $product->meta_description }}</td>
                                    </tr>
                                    @endif
                                    @if($product->meta_keywords)
                                    <tr>
                                        <th>Meta Keywords:</th>
                                        <td>{{ $product->meta_keywords }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($product->reviews && $product->reviews->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h4>Reviews ({{ $product->reviews->count() }})</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Rating</th>
                                                <th>Comment</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->reviews as $review)
                                                <tr>
                                                    <td>{{ $review->user->name ?? 'Guest' }}</td>
                                                    <td>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </td>
                                                    <td>{{ $review->comment ?? 'N/A' }}</td>
                                                    <td>{{ $review->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Order Statistics</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Total Orders:</th>
                                    <td>{{ $product->orderItems->count() ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Total Quantity Sold:</th>
                                    <td>{{ $product->orderItems->sum('quantity') ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <p class="text-muted">
                                <small>
                                    Created: {{ $product->created_at->format('M d, Y H:i') }} | 
                                    Updated: {{ $product->updated_at->format('M d, Y H:i') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

