<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     * Clients are global and should always use the main database.
     *
     * @var string
     */
    protected $connection = 'main';

    protected $fillable = [
        'name',
        'domain',
        'subdomain',
        'company_name',
        'contact_email',
        'contact_phone',
        'address',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'theme',
        'is_active',
        'subscription_expires_at',
        'settings',
        'database_name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_expires_at' => 'datetime',
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    // Note: The following are global application features, not client-specific:
    // - Settings: Use the 'settings' JSON column for client-specific configuration
    // - Banners: Global banners don't belong to specific clients
    // - Pages: Global pages (like terms, privacy) are shared across all clients

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function isSubscriptionActive()
    {
        if (!$this->subscription_expires_at) {
            return true; // No expiration set
        }
        return $this->subscription_expires_at->isFuture();
    }

    public function getSetting($key, $default = null)
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }
}
