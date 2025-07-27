<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'name',
        'phone',
        'email',
        'address_line1',
        'address_line2',
        'landmark',
        'city',
        'state',
        'country',
        'postal_code',
        'is_default_shipping',
        'is_default_billing',
        'delivery_instructions',
        'address_type',
        'is_verified',
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Address type constants
    const TYPE_HOME = 'home';
    const TYPE_WORK = 'work';
    const TYPE_OTHER = 'other';

    // Address usage constants
    const USAGE_SHIPPING = 'shipping';
    const USAGE_BILLING = 'billing';
    const USAGE_BOTH = 'both';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted full address
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address_line1;
        
        if ($this->address_line2) {
            $address .= ', ' . $this->address_line2;
        }
        
        if ($this->landmark) {
            $address .= ', ' . $this->landmark;
        }
        
        $address .= ', ' . $this->city . ', ' . $this->state . ' - ' . $this->postal_code;
        
        if ($this->country && $this->country !== 'India') {
            $address .= ', ' . $this->country;
        }
        
        return $address;
    }

    /**
     * Get address display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->label ?: ucfirst($this->type) . ' Address';
    }

    /**
     * Scope for default shipping addresses
     */
    public function scopeDefaultShipping($query)
    {
        return $query->where('is_default_shipping', true);
    }

    /**
     * Scope for default billing addresses
     */
    public function scopeDefaultBilling($query)
    {
        return $query->where('is_default_billing', true);
    }

    /**
     * Scope for shipping addresses
     */
    public function scopeForShipping($query)
    {
        return $query->whereIn('address_type', [self::USAGE_SHIPPING, self::USAGE_BOTH]);
    }

    /**
     * Scope for billing addresses
     */
    public function scopeForBilling($query)
    {
        return $query->whereIn('address_type', [self::USAGE_BILLING, self::USAGE_BOTH]);
    }

    /**
     * Get available address types
     */
    public static function getAddressTypes()
    {
        return [
            self::TYPE_HOME => 'Home',
            self::TYPE_WORK => 'Work',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get available address usage types
     */
    public static function getUsageTypes()
    {
        return [
            self::USAGE_SHIPPING => 'Shipping Only',
            self::USAGE_BILLING => 'Billing Only',
            self::USAGE_BOTH => 'Shipping & Billing',
        ];
    }

    /**
     * Set as default shipping address
     */
    public function setAsDefaultShipping()
    {
        // Remove default from other addresses
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default_shipping' => false]);
        
        // Set this as default
        $this->update(['is_default_shipping' => true]);
    }

    /**
     * Set as default billing address
     */
    public function setAsDefaultBilling()
    {
        // Remove default from other addresses
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default_billing' => false]);
        
        // Set this as default
        $this->update(['is_default_billing' => true]);
    }
}
