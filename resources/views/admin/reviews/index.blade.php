@extends('layouts.admin')

@section('title', 'Product Reviews')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product Reviews</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search" placeholder="Search reviews...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="status-filter">
                                <option value="">All Status</option>
                                <option value="1">Approved</option>
                                <option value="0">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="rating-filter">
                                <option value="">All Ratings</option>
                                @for($i=5; $i>=1; $i--)
                                    <option value="{{ $i }}">{{ $i }} Stars</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="date-from" placeholder="From Date">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-secondary" id="clear-filters">
                                <i class="fas fa-times"></i> Clear Filters
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="reviews-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>User</th>
                                    <th>Rating</th>
                                    <th>Title</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th width="160">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.products.show', $review->product_id) }}">{{ $review->product->name ?? '-' }}</a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.customers.show', $review->user->customer ?? 0) }}">{{ $review->user->name ?? '-' }}</a>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">{{ $review->rating }} <i class="fas fa-star text-warning"></i></span>
                                        </td>
                                        <td>{{ $review->title }}</td>
                                        <td>{{ Str::limit($review->comment, 40) }}</td>
                                        <td>
                                            @if($review->is_approved)
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $review->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$review->is_approved)
                                                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-secondary" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No reviews found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        setTimeout(function() { $('.alert').fadeOut('slow'); }, 5000);
        $('#search').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('#reviews-table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
        $('#status-filter, #rating-filter').change(function() {
            const status = $('#status-filter').val();
            const rating = $('#rating-filter').val();
            $('#reviews-table tbody tr').show().filter(function() {
                let show = true;
                if (status !== '' && ((status === '1' && !$(this).find('td:nth-child(6) .badge').hasClass('badge-success')) || (status === '0' && !$(this).find('td:nth-child(6) .badge').hasClass('badge-secondary')))) show = false;
                if (rating && !$(this).find('td:nth-child(3)').text().includes(rating)) show = false;
                return !show;
            }).hide();
        });
        $('#clear-filters').click(function() {
            $('#search').val('');
            $('#status-filter').val('');
            $('#rating-filter').val('');
            $('#date-from').val('');
            $('#reviews-table tbody tr').show();
        });
    });
</script>
@endpush 