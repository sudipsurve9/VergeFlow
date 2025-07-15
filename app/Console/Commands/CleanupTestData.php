<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\DB;

class CleanupTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:cleanup-test {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up test clients and their databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will delete test clients and their databases. Continue?')) {
            $this->info('Cleanup cancelled.');
            return;
        }

        $this->info('Cleaning up test data...');

        // Find test clients (excluding Vault64)
        $testClients = Client::where('name', '!=', 'Vault64')
                            ->where(function($query) {
                                $query->where('name', 'like', '%Test%')
                                      ->orWhere('subdomain', 'like', '%test%');
                            })->get();

        $this->info("Found {$testClients->count()} test clients to clean up.");

        foreach ($testClients as $client) {
            $this->cleanupClient($client);
        }

        $this->info('Test data cleanup completed!');
    }

    private function cleanupClient(Client $client)
    {
        $this->info("Cleaning up client: {$client->name} (ID: {$client->id})");

        // Delete the database if it exists
        if ($client->database_name) {
            try {
                $config = config('database.connections.mysql');
                $pdo = new \PDO(
                    "mysql:host={$config['host']};port={$config['port']}",
                    $config['username'],
                    $config['password']
                );
                
                $pdo->exec("DROP DATABASE IF EXISTS `{$client->database_name}`");
                $this->info("  ✓ Dropped database: {$client->database_name}");
            } catch (\Exception $e) {
                $this->warn("  ⚠ Failed to drop database: " . $e->getMessage());
            }
        }

        // Delete the client record
        try {
            $client->delete();
            $this->info("  ✓ Deleted client record");
        } catch (\Exception $e) {
            $this->error("  ✗ Failed to delete client record: " . $e->getMessage());
        }
    }
} 