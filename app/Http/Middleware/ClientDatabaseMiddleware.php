<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use App\Services\DatabaseService;

class ClientDatabaseMiddleware
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
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Super admin can access all databases
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Set client context for database connections
        if ($user->client_id) {
            $client = Client::on('main')->find($user->client_id);
            if ($client && $client->database_name) {
                session(['current_client_id' => $client->id]);
                
                // Set up client database connection
                $databaseService = new DatabaseService();
                try {
                    $connectionName = $databaseService->getClientConnection($client);
                    config(['database.default' => $connectionName]);
                    
                    // Also set the connection for models using MultiTenant trait
                    app()->instance('tenant.connection', $connectionName);
                    
                } catch (\Exception $e) {
                    \Log::error('Failed to set client database connection: ' . $e->getMessage());
                }
            }
        }
        
        return $next($request);
    }
} 