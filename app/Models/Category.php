<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductReview;
use App\Traits\HasClientScope;
use App\Traits\HasClientDatabase;

class Category extends Model
{
    use HasFactory, HasClientScope, HasClientDatabase {
        HasClientDatabase::scopeForClient insteadof HasClientScope;
        HasClientScope::scopeForClient as scopeForClientScope;
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'client_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(ProductReview::class, Product::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
