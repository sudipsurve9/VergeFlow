<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateClientDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:migrate 
                            {--client= : Specific client ID to migrate}
                            {--fresh : Run fresh migrations (drops all tables)}
                            {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations on all client databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Client Database Migration Tool ===');

        $clientId = $this->option('client');
        $fresh = $this->option('fresh');
        $force = $this->option('force');

        if (!$force && $this->laravel->environment('production')) {
            if (!$this->confirm('⚠️  Are you sure you want to run migrations in PRODUCTION?', false)) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        $databaseService = new DatabaseService();

        // Get clients to migrate
        if ($clientId) {
            $clients = Client::on('main')->where('id', $clientId)->get();
            if ($clients->isEmpty()) {
                $this->error("Client with ID {$clientId} not found.");
                return 1;
            }
        } else {
            $clients = Client::on('main')
                ->whereNotNull('database_name')
                ->get();
        }

        if ($clients->isEmpty()) {
            $this->error('No clients found to migrate.');
            return 1;
        }

        $this->info("Found {$clients->count()} client(s) to migrate.\n");

        $successCount = 0;
        $failCount = 0;

        foreach ($clients as $client) {
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("Migrating: {$client->name} (ID: {$client->id})");
            $this->info("Database: {$client->database_name}");

            try {
                // Get or create connection
                $connectionName = $databaseService->getClientConnection($client);

                // Verify connection
                DB::connection($connectionName)->getPdo();
                $this->info("✓ Connection: {$connectionName}");

                // Set the default connection temporarily to avoid main-db migrations
                $originalDefault = config('database.default');
                config(['database.default' => $connectionName]);
                
                try {
                    // Run migrations
                    if ($fresh) {
                        $this->warn("⚠️  Running FRESH migrations (will DROP all tables!)");
                        if (!$this->confirm('Continue?', false)) {
                            $this->warn("Skipped {$client->name}");
                            continue;
                        }
                        
                        Artisan::call('migrate:fresh', [
                            '--database' => $connectionName,
                            '--force' => true,
                            '--path' => 'database/migrations',
                        ]);
                    } else {
                        Artisan::call('migrate', [
                            '--database' => $connectionName,
                            '--force' => true,
                        ]);
                    }
                } finally {
                    // Restore original default connection
                    config(['database.default' => $originalDefault]);
                }

                $output = trim(Artisan::output());
                if (!empty($output)) {
                    $this->line($output);
                }

                $this->info("✓ Successfully migrated {$client->name}\n");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed: " . $e->getMessage());
                $this->error("Error type: " . get_class($e));
                
                // Always show trace in production for debugging
                if ($this->laravel->environment('production') || $this->option('verbose')) {
                    $this->error("File: " . $e->getFile() . ":" . $e->getLine());
                    $this->error("Trace: " . $e->getTraceAsString());
                }
                
                $failCount++;
                $this->newLine();
            }
        }

        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("=== Migration Summary ===");
        $this->info("✓ Successful: {$successCount}");
        if ($failCount > 0) {
            $this->error("✗ Failed: {$failCount}");
        }
        $this->info("Total: " . ($successCount + $failCount));

        return $failCount > 0 ? 1 : 0;
    }
}

