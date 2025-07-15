<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect based on their role
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->isSuperAdmin()) {
                return redirect()->route('super_admin.dashboard')
                    ->with('info', 'You are logged in as Super Admin. Please logout to access Customer portal.');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'You are logged in as Admin. Please logout to access Customer portal.');
            } else {
                return redirect()->route('home')
                    ->with('info', 'You are already logged in as Customer.');
            }
        }
        
        return view('auth.login');
    }
}
