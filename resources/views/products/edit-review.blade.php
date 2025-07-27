@extends('layouts.app_modern')

@section('title', 'Edit Review - ' . $review->product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li><a href="{{ route('profile.reviews') }}" class="hover:text-blue-600">My Reviews</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-900">Edit Review</li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Product Info Header -->
            <div class="bg-gray-50 px-6 py-4 border-b">
                <div class="flex items-center space-x-4">
                    @if($review->product->image)
                        <img src="{{ Storage::url($review->product->image) }}" alt="{{ $review->product->name }}" class="w-16 h-16 object-cover rounded">
                    @endif
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">Edit Your Review</h1>
                        <p class="text-gray-600">{{ $review->product->name }}</p>
                        @if($review->is_verified_purchase)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Verified Purchase
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Review Form -->
            <form action="{{ route('reviews.update', $review) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Rating -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating *</label>
                    <div class="flex items-center space-x-1" id="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn text-2xl {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 focus:outline-none" data-rating="{{ $i }}">
                                <i class="fas fa-star"></i>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ $review->rating }}" required>
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Review Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $review->title) }}"
                           placeholder="Summarize your review in a few words"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Text -->
                <div class="mb-6">
                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">Your Review *</label>
                    <textarea id="review" 
                              name="review" 
                              rows="6"
                              placeholder="Tell others about your experience with this product..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              required>{{ old('review', $review->review) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Minimum 10 characters required</p>
                    @error('review')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Images -->
                @if($review->images && count($review->images) > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                        <div class="flex space-x-2">
                            @foreach($review->images as $image)
                                <img src="{{ Storage::url($image) }}" 
                                     alt="Review image" 
                                     class="w-20 h-20 object-cover rounded">
                            @endforeach
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Upload new images below to replace current ones</p>
                    </div>
                @endif

                <!-- New Images -->
                <div class="mb-6">
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Update Photos (Optional)</label>
                    <input type="file" 
                           id="images" 
                           name="images[]" 
                           multiple
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">You can upload up to 5 images (JPEG, PNG only)</p>
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between pt-6 border-t">
                    <a href="{{ route('profile.reviews') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Update Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-btn');
    const ratingInput = document.getElementById('rating-input');
    let selectedRating = {{ $review->rating }};

    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            selectedRating = index + 1;
            ratingInput.value = selectedRating;
            updateStars();
        });

        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
    });

    document.getElementById('rating-stars').addEventListener('mouseleave', function() {
        updateStars();
    });

    function updateStars() {
        stars.forEach((star, index) => {
            if (index < selectedRating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    function highlightStars(rating) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }
});
</script>
@endsection
