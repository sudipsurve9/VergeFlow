<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SuperAdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        // If user is already logged in, redirect based on their role
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->isSuperAdmin()) {
                return redirect()->route('super_admin.dashboard')
                    ->with('info', 'You are already logged in as Super Admin.');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'You are logged in as Admin. Please logout to access Super Admin portal.');
            } else {
                return redirect()->route('home')
                    ->with('info', 'You are logged in as Customer. Please logout to access Super Admin portal.');
            }
        }
        
        return view('auth.super_admin_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Only allow super admin access
            if ($user->isSuperAdmin()) {
                $request->session()->regenerate();
                
                return redirect()->intended(route('super_admin.dashboard'))
                    ->with('success', 'Welcome to Super Admin Portal!');
            } else {
                // User is not super admin, logout and show error
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Access denied. Super admin privileges required.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid super admin credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('super_admin.login')
            ->with('success', 'You have been successfully logged out from Super Admin Portal.');
    }
}
