<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RememberMeService;

class RememberMeMiddleware
{
    /**
     * Handle an incoming request.
     * Automatically extend session for users with "Remember Me" enabled
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Extend session for remembered users on each request
        RememberMeService::extendSessionIfRemembered();
        
        return $next($request);
    }
}
