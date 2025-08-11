<?php

namespace App\Traits;

use App\Services\MultiTenantService;

trait MultiTenant
{
    /**
     * Boot the multi-tenant trait
     */
    protected static function bootMultiTenant()
    {
        // Set the connection based on current tenant context
        static::creating(function ($model) {
            $model->setTenantConnection();
        });
        
        static::updating(function ($model) {
            $model->setTenantConnection();
        });
        
        static::deleting(function ($model) {
            $model->setTenantConnection();
        });
    }
    
    /**
     * Set the appropriate database connection for this model
     */
    public function setTenantConnection()
    {
        $multiTenantService = app(MultiTenantService::class);
        
        // Determine if this model should use client or main database
        if ($this->isClientSpecificModel()) {
            $clientId = $this->getClientId();
            if ($clientId) {
                $connection = $multiTenantService->getClientConnection($clientId);
                $this->setConnection($connection);
            }
        } else {
            // Use main database for global models
            $this->setConnection('main');
        }
    }
    
    /**
     * Determine if this model is client-specific
     */
    protected function isClientSpecificModel(): bool
    {
        // Models that should be in client databases
        $clientModels = [
            'App\Models\Product',
            'App\Models\Category', 
            'App\Models\Order',
            'App\Models\OrderItem',
            'App\Models\CartItem',
            'App\Models\Customer',
            'App\Models\Coupon',
            'App\Models\Address',
            'App\Models\ProductReview',
            'App\Models\RecentlyViewed',
        ];
        
        return in_array(get_class($this), $clientModels);
    }
    
    /**
     * Get the client ID for this model
     */
    protected function getClientId(): ?int
    {
        // If model has client_id attribute, use it
        if (isset($this->attributes['client_id'])) {
            return $this->attributes['client_id'];
        }
        
        // If user is authenticated and has client_id, use it
        if (auth()->check() && auth()->user()->client_id) {
            return auth()->user()->client_id;
        }
        
        // Check session for client context
        if (session('client_id')) {
            return session('client_id');
        }
        
        return null;
    }
    
    /**
     * Scope to current tenant
     */
    public function scopeForCurrentTenant($query)
    {
        $clientId = $this->getClientId();
        
        if ($clientId && $this->isClientSpecificModel()) {
            return $query->where('client_id', $clientId);
        }
        
        return $query;
    }
}
