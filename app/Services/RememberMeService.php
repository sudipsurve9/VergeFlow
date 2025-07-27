<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Config;

class RememberMeService
{
    /**
     * Handle "Remember Me" functionality like Amazon
     * Extends session duration when user checks "Remember Me"
     */
    public static function handleRememberMe($remember = false)
    {
        if ($remember) {
            // Set remember me cookie for 30 days (like Amazon)
            $duration = Config::get('session.remember_me_duration', 43200); // 30 days in minutes
            
            // Extend the session lifetime for remembered users
            Config::set('session.lifetime', $duration);
            
            // Set a custom remember cookie
            Cookie::queue('remember_user', true, $duration);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user should be remembered
     */
    public static function shouldRememberUser()
    {
        return Cookie::get('remember_user', false);
    }
    
    /**
     * Clear remember me cookie on logout
     */
    public static function forgetUser()
    {
        Cookie::queue(Cookie::forget('remember_user'));
    }
    
    /**
     * Extend session for remembered users on each request
     */
    public static function extendSessionIfRemembered()
    {
        if (Auth::check() && self::shouldRememberUser()) {
            $duration = Config::get('session.remember_me_duration', 43200);
            Config::set('session.lifetime', $duration);
            
            // Refresh the remember cookie
            Cookie::queue('remember_user', true, $duration);
        }
    }
}
