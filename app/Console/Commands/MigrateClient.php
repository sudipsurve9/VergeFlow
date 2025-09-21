<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MultiTenantService;
use Illuminate\Support\Facades\Artisan;
use App\Models\Client;

class MigrateClient extends Command
{
    protected $signature = 'tenants:migrate {clientId : The client ID to migrate} {--seed : Run seeders after migrate}';

    protected $description = 'Run migrations (and optional seed) for a specific client database by client ID';

    public function handle(): int
    {
        $clientId = (int)$this->argument('clientId');

        $client = Client::on('main')->find($clientId);
        if (!$client) {
            $this->error("Client {$clientId} not found in main database.");
            return self::FAILURE;
        }

        $dbName = $client->database_name ?: "vergeflow_client_{$clientId}";
        $this->info("Using database: {$dbName}");

        // Set client connection to the proper database name
        $mts = new MultiTenantService();
        $mts->setClientDatabaseConnection($clientId, $dbName);
        $connectionName = $mts->getClientConnection($clientId);

        // Run all migrations for this connection
        $this->info("Running migrations on connection: {$connectionName}");
        Artisan::call('migrate', [
            '--database' => $connectionName,
            '--force' => true,
        ]);
        $this->output->write(Artisan::output());

        if ($this->option('seed')) {
            $this->info('Running seeders...');
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
            $this->output->write(Artisan::output());
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
