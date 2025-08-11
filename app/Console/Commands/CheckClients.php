<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\DB;

class CheckClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check current clients in the system';

    protected $multiTenantService;

    public function __construct(MultiTenantService $multiTenantService)
    {
        parent::__construct();
        $this->multiTenantService = $multiTenantService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📊 Checking Current Clients in VergeFlow System...');
        $this->newLine();

        // Switch to main database
        $this->multiTenantService->switchToMainDatabase();

        $clients = Client::all();
        
        $this->info("📈 Total Clients Found: {$clients->count()}");
        $this->newLine();

        if ($clients->count() > 0) {
            $this->info('📋 Client Details:');
            $this->line('----------------------------------------');
            
            foreach ($clients as $client) {
                $this->line("🏢 Client ID: {$client->id}");
                $this->line("   Name: {$client->name}");
                $this->line("   Company: {$client->company_name}");
                $this->line("   Domain: {$client->domain}");
                $this->line("   Subdomain: {$client->subdomain}");
                $this->line("   Status: {$client->status}");
                $this->line("   Theme: {$client->theme}");
                $this->line("   Created: {$client->created_at}");
                
                // Check if client database exists
                try {
                    $this->multiTenantService->switchToClientDatabase($client->id);
                    $dbName = DB::getDatabaseName();
                    $this->line("   Database: {$dbName} ✅");
                    
                    // Get some stats from client database
                    $products = DB::table('products')->count();
                    $orders = DB::table('orders')->count();
                    $customers = DB::table('customers')->count();
                    
                    $this->line("   Stats: {$products} products, {$orders} orders, {$customers} customers");
                    
                } catch (\Exception $e) {
                    $this->line("   Database: ❌ Error - " . $e->getMessage());
                }
                
                $this->line('----------------------------------------');
            }
        } else {
            $this->warn('⚠️ No clients found in the system.');
            $this->info('💡 You can create a new client using the super admin portal:');
            $this->info('   URL: http://127.0.0.1:8000/super-admin/clients/create');
        }

        // Switch back to main database
        $this->multiTenantService->switchToMainDatabase();
        
        $this->newLine();
        $this->info('✅ Client check completed!');
    }
}
