@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card" role="region" aria-label="Coupons management">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Coupons</h3>
                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary" aria-label="Add new coupon">
                        <i class="fas fa-plus"></i> Add Coupon
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert" aria-label="Success message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert" aria-label="Error message">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" aria-label="Close alert">×</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" role="table" aria-label="Coupons table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Status</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                    <tr>
                                        <td>{{ $coupon->id }}</td>
                                        <td><strong>{{ $coupon->code }}</strong></td>
                                        <td>{{ $coupon->name }}</td>
                                        <td>{{ ucfirst($coupon->type) }}</td>
                                        <td>{{ $coupon->value }}</td>
                                        <td>
                                            @if($coupon->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $coupon->start_date }}</td>
                                        <td>{{ $coupon->end_date }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-warning" title="Edit" aria-label="Edit coupon {{ $coupon->code }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" style="display: inline;" aria-label="Delete coupon {{ $coupon->code }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" aria-label="Delete coupon {{ $coupon->code }}" onclick="return confirm('Are you sure you want to delete this coupon?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No coupons found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 