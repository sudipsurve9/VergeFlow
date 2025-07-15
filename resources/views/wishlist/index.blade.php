@extends($layout)

@section('content')
<div class="container py-5" role="main" aria-label="Wishlist main content">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card premium-card neon-glow" role="region" aria-label="Wishlist items">
                <div class="card-header premium-header text-center">
                    <h2 class="mb-0 neon-glow"><i class="fas fa-heart me-2"></i>My Wishlist</h2>
                </div>
                <div class="card-body">
                    @if($wishlists->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-heart-broken fa-3x mb-3"></i>
                            <h4>No items in your wishlist yet.</h4>
                            <a href="{{ route('products.index') }}" class="btn btn-accent mt-3" aria-label="Browse products">Browse Products</a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($wishlists as $wishlist)
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset('storage/products/' . ($wishlist->product->image ?? '')) }}" alt="{{ $wishlist->product->name ?? '' }}" class="rounded me-3" style="width:60px;height:60px;object-fit:cover;">
                                        <div>
                                            <a href="{{ route('products.show', $wishlist->product->slug) }}" class="fw-bold product-title-glow" aria-label="View {{ $wishlist->product->name }}">
                                                {{ $wishlist->product->name }}
                                            </a>
                                            <div class="text-muted small">₹{{ number_format($wishlist->product->price, 2) }}</div>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('wishlist.destroy', $wishlist->id) }}" onsubmit="return confirm('Remove this product from your wishlist?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Remove from wishlist"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 