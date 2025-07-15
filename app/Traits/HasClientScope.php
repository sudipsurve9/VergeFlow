<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasClientScope
{
    /**
     * Boot the trait
     */
    protected static function bootHasClientScope()
    {
        static::addGlobalScope('client', function (Builder $builder) {
            // Prevent recursion: do not apply to User model
            if ($builder->getModel() instanceof \App\Models\User) {
                return;
            }
            $user = Auth::user();
            
            // If no user is authenticated, allow public access to all data
            if (!$user) {
                return;
            }
            
            // Super admin can see all data
            if ($user->isSuperAdmin()) {
                return;
            }
            
            // All other users can only see their client's data
            $clientId = $user->getClientId();
            if ($clientId !== null) {
                $builder->where('client_id', $clientId);
            }
        });
    }

    /**
     * Scope to get data for a specific client
     */
    public function scopeForClient(Builder $query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to get data for the current user's client
     */
    public function scopeForCurrentClient(Builder $query)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin()) {
            $clientId = $user->getClientId();
            if ($clientId !== null) {
                return $query->where('client_id', $clientId);
            }
        }
        return $query;
    }
} 