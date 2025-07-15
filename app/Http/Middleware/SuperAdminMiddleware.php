<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('super_admin.login')->with('error', 'Access denied. Please login.');
        }
        
        if (!$user->isSuperAdmin()) {
            return redirect()->route('super_admin.login')->with('error', 'Access denied. Super admin privileges required.');
        }
        
        return $next($request);
    }
}
