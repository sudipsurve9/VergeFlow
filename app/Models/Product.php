<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductReview;
use App\Traits\HasClientScope;
use App\Traits\HasClientDatabase;
use App\Traits\MultiTenant;

class Product extends Model
{
    use HasFactory, MultiTenant;
    
    /**
     * Override newQuery to ensure correct database connection
     */
    public function newQuery()
    {
        // Force use of tenant connection if available
        if (app()->bound('tenant.connection')) {
            $this->setConnection(app('tenant.connection'));
        } elseif (config('database.default') !== 'main') {
            $this->setConnection(config('database.default'));
        }
        
        return parent::newQuery();
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'image',
        'images',
        'is_featured',
        'is_active',
        'category_id',
        'client_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->approved();
    }

    public function recentlyViewed()
    {
        return $this->hasMany(RecentlyViewed::class);
    }

    // Rating calculations
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?: 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function getRatingStarsAttribute()
    {
        $rating = round($this->average_rating);
        return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    }

    public function getRatingPercentageAttribute()
    {
        return ($this->average_rating / 5) * 100;
    }
}
