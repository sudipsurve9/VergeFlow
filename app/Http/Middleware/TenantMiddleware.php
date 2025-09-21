<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MultiTenantService;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        $this->multiTenantService = $multiTenantService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Determine which database to use based on context
        $this->setDatabaseContext($request);

        return $next($request);
    }

    /**
     * Set the appropriate database context based on the request
     */
    private function setDatabaseContext(Request $request): void
    {
        // Check if this is a super admin or admin route
        if ($this->isSuperAdminRoute($request) || $this->isAdminRoute($request)) {
            // Use main database for super admin and admin operations
            $this->multiTenantService->switchToMainDatabase();
            return;
        }

        // For site routes, determine client and switch to client database
        $clientId = $this->determineClientId($request);
        
        if ($clientId) {
            // Persist client context for downstream usage (controllers, views)
            session(['current_client_id' => $clientId]);
            $this->multiTenantService->switchToClientDatabase($clientId);
        } else {
            // Fallback to main database if client cannot be determined
            $this->multiTenantService->switchToMainDatabase();
        }
    }

    /**
     * Check if this is a super admin route
     */
    private function isSuperAdminRoute(Request $request): bool
    {
        return $request->is('super-admin*') || 
               $request->is('api/super-admin*');
    }

    /**
     * Check if this is an admin route
     */
    private function isAdminRoute(Request $request): bool
    {
        return $request->is('admin*') || 
               $request->is('api/admin*');
    }

    /**
     * Determine the client ID based on various factors
     */
    private function determineClientId(Request $request): ?int
    {
        // Method 1: Check if user is logged in and has a client_id
        if (Auth::check() && Auth::user()->client_id) {
            return Auth::user()->client_id;
        }

        // Method 2: Check subdomain
        $subdomain = $this->getSubdomain($request);
        if ($subdomain && $subdomain !== 'www') {
            $client = Client::where('subdomain', $subdomain)->first();
            if ($client) {
                return $client->id;
            }
        }

        // Method 3: Check domain
        $domain = $request->getHost();
        $client = Client::where('domain', $domain)->first();
        if ($client) {
            return $client->id;
        }

        // Method 4: Check for client parameter in request
        if ($request->has('client_id')) {
            return $request->get('client_id');
        }

        // Method 5: Check session for client context
        if (session('client_id')) {
            return session('client_id');
        }

        // Method 6: Check DEFAULT_CLIENT_ID from env (useful for localhost/dev)
        $defaultId = env('DEFAULT_CLIENT_ID');
        if (!empty($defaultId)) {
            return (int) $defaultId;
        }

        // Default: Use first client for development (you might want to remove this in production)
        $firstClient = Client::first();
        return $firstClient ? $firstClient->id : null;
    }

    /**
     * Extract subdomain from request
     */
    private function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // If we have at least 3 parts (subdomain.domain.tld), return the first part
        if (count($parts) >= 3) {
            return $parts[0];
        }
        
        return null;
    }
}
