<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
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
            return redirect()->route('admin.login')->with('error', 'Access denied. Not authenticated.');
        }
        if (!method_exists($user, 'isAdmin')) {
            return redirect()->route('admin.login')->with('error', 'Access denied. Admin check missing.');
        }
        if (!$user->isAdmin()) {
            return redirect()->route('admin.login')->with('error', 'Access denied. Admin privileges required.');
        }
        return $next($request);
    }
}
