@extends('layouts.app_modern')

@section('title', 'My Reviews')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold neon-glow mb-2">My Reviews</h1>
            <p class="text-gray-600">Manage your product reviews and ratings</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <a href="{{ route('profile.edit') }}" class="text-gray-500 hover:text-blue-600 pb-2">
                    <i class="fas fa-user me-2"></i>Profile
                </a>
                <a href="{{ route('profile.reviews') }}" class="text-blue-600 border-b-2 border-blue-600 pb-2">
                    <i class="fas fa-star me-2"></i>My Reviews
                </a>
            </nav>
        </div>

        @if($reviews->count() > 0)
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden theme-card">
                        <!-- Product Info Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    @if($review->product->image)
                                        <img src="{{ Storage::url($review->product->image) }}" 
                                             alt="{{ $review->product->name }}" 
                                             class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold">
                                            <a href="{{ route('products.show', $review->product->slug) }}" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ $review->product->name }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600">{{ $review->product->category->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                                    @if($review->is_verified_purchase)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Verified Purchase
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Review Content -->
                        <div class="p-6">
                            <!-- Rating -->
                            <div class="flex items-center mb-3">
                                <div class="rating-stars text-yellow-400 text-xl mr-3">
                                    {{ $review->stars }}
                                </div>
                                <span class="text-gray-600">{{ $review->rating }}/5 stars</span>
                            </div>

                            <!-- Review Title -->
                            <h4 class="text-xl font-semibold mb-2">{{ $review->title }}</h4>

                            <!-- Review Text -->
                            <p class="text-gray-700 mb-4">{{ $review->review }}</p>

                            <!-- Review Images -->
                            @if($review->images && count($review->images) > 0)
                                <div class="mb-4">
                                    <div class="flex space-x-2">
                                        @foreach($review->images as $image)
                                            <img src="{{ Storage::url($image) }}" 
                                                 alt="Review image" 
                                                 class="w-20 h-20 object-cover rounded cursor-pointer hover:opacity-80">
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Review Stats -->
                            <div class="flex items-center justify-between pt-4 border-t">
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-thumbs-up mr-1"></i>
                                        {{ $review->helpful_count }} found this helpful
                                    </span>
                                    @if($review->is_approved)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending Approval
                                        </span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <a href="{{ route('reviews.edit', $review) }}" 
                                       class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <button onclick="deleteReview({{ $review->id }})" 
                                            class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($reviews->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $reviews->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-star fa-4x text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Reviews Yet</h3>
                    <p class="text-gray-600 mb-6">You haven't written any product reviews yet. Start by purchasing and reviewing products!</p>
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-shopping-bag mr-2"></i>
                        Shop Products
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/reviews/${reviewId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        form.appendChild(methodInput);
        form.appendChild(tokenInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
