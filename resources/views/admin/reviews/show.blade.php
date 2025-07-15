@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Review Details</h3>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reviews
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Product</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.products.show', $review->product_id) }}">{{ $review->product->name ?? '-' }}</a>
                        </dd>
                        <dt class="col-sm-4">User</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.customers.show', $review->user->customer ?? 0) }}">{{ $review->user->name ?? '-' }}</a>
                        </dd>
                        <dt class="col-sm-4">Rating</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-warning">{{ $review->rating }} <i class="fas fa-star text-warning"></i></span>
                        </dd>
                        <dt class="col-sm-4">Title</dt>
                        <dd class="col-sm-8">{{ $review->title }}</dd>
                        <dt class="col-sm-4">Comment</dt>
                        <dd class="col-sm-8">{{ $review->comment }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
                            @if($review->is_approved)
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-secondary">Pending</span>
                            @endif
                        </dd>
                        <dt class="col-sm-4">Verified Purchase</dt>
                        <dd class="col-sm-8">
                            @if($review->is_verified_purchase)
                                <span class="badge badge-info">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </dd>
                        <dt class="col-sm-4">Created At</dt>
                        <dd class="col-sm-8">{{ $review->created_at->format('M d, Y H:i') }}</dd>
                    </dl>
                    <div class="mt-3">
                        @if(!$review->is_approved)
                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success mr-2"><i class="fas fa-check"></i> Approve</button>
                            </form>
                        @else
                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this review?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 