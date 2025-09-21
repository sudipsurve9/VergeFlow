<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        View::composer('*', function ($view) {
            // Always use main database for global settings
            $layout = Setting::on('main')->where('key', 'site_layout')->value('value') ?? 'layouts.app_modern';
            $view->with('layout', $layout);

            // Resolve current client (for contact info etc.) and share globally
            try {
                $host = request()->getHost();
                $parts = explode('.', $host);
                $sub = count($parts) >= 3 ? $parts[0] : null;

                $currentClient = null;
                // 1) If logged-in user belongs to a client, prefer that
                if (auth()->check() && auth()->user()->client_id) {
                    $currentClient = \App\Models\Client::on('main')->find(auth()->user()->client_id);
                }
                // 2) Try subdomain
                if (!$currentClient && $sub && $sub !== 'www') {
                    $currentClient = \App\Models\Client::on('main')->where('subdomain', $sub)->first();
                }
                // 3) Try full domain
                if (!$currentClient) {
                    $currentClient = \App\Models\Client::on('main')->where('domain', $host)->first();
                }
                // 4) Try session client context
                if (!$currentClient && session('client_id')) {
                    $currentClient = \App\Models\Client::on('main')->find(session('client_id'));
                }
                // 5) Try DEFAULT_CLIENT_ID from env if provided
                if (!$currentClient && env('DEFAULT_CLIENT_ID')) {
                    $currentClient = \App\Models\Client::on('main')->find((int) env('DEFAULT_CLIENT_ID'));
                }
                // 6) Try a known default subdomain (development convenience)
                if (!$currentClient) {
                    $currentClient = \App\Models\Client::on('main')->where('subdomain', 'vault64')->first();
                }
                // 7) Fallback to first client
                if (!$currentClient) {
                    $currentClient = \App\Models\Client::on('main')->orderBy('id')->first();
                }
            } catch (\Throwable $e) {
                $currentClient = null;
            }

            $contactPhone = $currentClient->contact_phone ?? '+1 (234) 567-890';
            $contactEmail = $currentClient->contact_email ?? 'info@vault64.com';
            $telHref = '+' . preg_replace('/[^0-9]/', '', ltrim($contactPhone, '+'));

            $view->with(compact('currentClient', 'contactPhone', 'contactEmail', 'telHref'));
        });
    }
}
