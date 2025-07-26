<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginController extends Controller
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
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'You are already logged in as Admin.');
            } else {
                return redirect()->route('home')
                    ->with('info', 'You are logged in as Customer. Please logout to access Admin portal.');
            }
        }
        return view('auth.admin_login');
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
            // Check if user is admin or super admin
            if ($user->isAdmin() || $user->isSuperAdmin()) {
                $request->session()->regenerate();
                // Both admins and super admins go to admin dashboard
                    return redirect()->intended(route('admin.dashboard'))
                        ->with('success', 'Welcome back, Admin!');
            } else {
                // User is not admin, logout and show error
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'You do not have admin privileges.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login')
            ->with('success', 'You have been successfully logged out.');
    }
}
