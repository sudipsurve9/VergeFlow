<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\Auth;

class SetClientDatabase
{
    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        $this->multiTenantService = $multiTenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get client ID from authenticated user or session
        $clientId = null;
        
        if (Auth::check() && Auth::user()->client_id) {
            $clientId = Auth::user()->client_id;
        } elseif (session('client_id')) {
            $clientId = session('client_id');
        }

        // If we have a client ID, switch to client database
        if ($clientId) {
            $this->multiTenantService->switchToClientDatabase($clientId);
            
            // Log for debugging
            \Log::info('SetClientDatabase middleware', [
                'client_id' => $clientId,
                'connection' => $this->multiTenantService->getClientConnection($clientId),
                'default_connection' => config('database.default')
            ]);
        } else {
            // Ensure we're using main database if no client ID
            $this->multiTenantService->switchToMainDatabase();
            
            \Log::warning('SetClientDatabase middleware: No client ID found, using main database');
        }

        return $next($request);
    }
}
