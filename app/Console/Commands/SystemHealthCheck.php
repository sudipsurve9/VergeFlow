<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use App\Services\DatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:health-check 
                            {--detailed : Show detailed information}
                            {--fix : Attempt to fix issues found}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the health of the VergeFlow multi-database system';

    private $issues = [];
    private $warnings = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VergeFlow System Health Check ===');
        
        $startTime = microtime(true);
        
        // Run all health checks
        $this->checkMainDatabase();
        $this->checkClientDatabases();
        $this->checkDatabaseConnections();
        $this->checkClientData();
        $this->checkSystemPerformance();
        $this->checkStorageSpace();
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        // Display results
        $this->displayResults($executionTime);
        
        // Fix issues if requested
        if ($this->option('fix') && !empty($this->issues)) {
            $this->fixIssues();
        }
        
        return empty($this->issues) ? 0 : 1;
    }
    
    private function checkMainDatabase()
    {
        $this->info('1. Checking main database...');
        
        try {
            DB::connection('mysql')->getPdo();
            $this->info('   ✓ Main database connection successful');
            
            // Check if main database has required tables
            $tables = ['clients', 'users', 'migrations'];
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->info("   ✓ Table '{$table}' exists");
                } else {
                    $this->issues[] = "Main database missing table: {$table}";
                }
            }
            
        } catch (\Exception $e) {
            $this->issues[] = "Main database connection failed: " . $e->getMessage();
        }
    }
    
    private function checkClientDatabases()
    {
        $this->info('2. Checking client databases...');
        
        $clients = Client::all();
        $this->info("   Found {$clients->count()} clients");
        
        $databaseService = new DatabaseService();
        
        foreach ($clients as $client) {
            $this->info("   Checking client: {$client->name}");
            
            if (!$client->database_name) {
                $this->issues[] = "Client {$client->name} has no database name";
                continue;
            }
            
            // Check if database exists
            try {
                $config = config('database.connections.mysql');
                $pdo = new \PDO(
                    "mysql:host={$config['host']};port={$config['port']}",
                    $config['username'],
                    $config['password']
                );
                
                $stmt = $pdo->query("SHOW DATABASES LIKE '{$client->database_name}'");
                if ($stmt->rowCount() > 0) {
                    $this->info("     ✓ Database exists: {$client->database_name}");
                    
                    // Check database connection
                    try {
                        $connection = $databaseService->getClientConnection($client);
                        $this->info("     ✓ Database connection working");
                    } catch (\Exception $e) {
                        $this->issues[] = "Client {$client->name} database connection failed: " . $e->getMessage();
                    }
                } else {
                    $this->issues[] = "Client {$client->name} database does not exist: {$client->database_name}";
                }
                
            } catch (\Exception $e) {
                $this->issues[] = "Failed to check client {$client->name} database: " . $e->getMessage();
            }
        }
    }
    
    private function checkDatabaseConnections()
    {
        $this->info('3. Testing database connections...');
        
        $clients = Client::whereNotNull('database_name')->get();
        
        foreach ($clients as $client) {
            try {
                $databaseService = new DatabaseService();
                $connection = $databaseService->getClientConnection($client);
                
                // Test a simple query
                DB::connection($connection)->select('SELECT 1');
                $this->info("   ✓ Client {$client->name} connection working");
                
            } catch (\Exception $e) {
                $this->issues[] = "Client {$client->name} connection test failed: " . $e->getMessage();
            }
        }
    }
    
    private function checkClientData()
    {
        $this->info('4. Checking client data integrity...');
        
        $clients = Client::whereNotNull('database_name')->get();
        
        foreach ($clients as $client) {
            try {
                $databaseService = new DatabaseService();
                $connection = $databaseService->getClientConnection($client);
                
                // Check if client has basic data
                $productCount = DB::connection($connection)->table('products')->count();
                $userCount = DB::connection($connection)->table('users')->count();
                
                if ($productCount == 0 && $userCount == 0) {
                    $this->warnings[] = "Client {$client->name} has no data";
                } else {
                    $this->info("   ✓ Client {$client->name} has {$productCount} products, {$userCount} users");
                }
                
            } catch (\Exception $e) {
                $this->issues[] = "Failed to check client {$client->name} data: " . $e->getMessage();
            }
        }
    }
    
    private function checkSystemPerformance()
    {
        $this->info('5. Checking system performance...');
        
        // Check memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        
        $this->info("   Memory usage: " . $this->formatBytes($memoryUsage));
        $this->info("   Memory limit: {$memoryLimit}");
        
        if ($memoryUsage > 100 * 1024 * 1024) { // 100MB
            $this->warnings[] = "High memory usage detected";
        }
        
        // Check database query performance
        $startTime = microtime(true);
        Client::count();
        $queryTime = (microtime(true) - $startTime) * 1000;
        
        $this->info("   Query performance: " . round($queryTime, 2) . "ms");
        
        if ($queryTime > 100) { // 100ms
            $this->warnings[] = "Slow database queries detected";
        }
    }
    
    private function checkStorageSpace()
    {
        $this->info('6. Checking storage space...');
        
        $storagePath = storage_path();
        $freeSpace = disk_free_space($storagePath);
        $totalSpace = disk_total_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = ($usedSpace / $totalSpace) * 100;
        
        $this->info("   Storage usage: " . round($usagePercentage, 2) . "%");
        $this->info("   Free space: " . $this->formatBytes($freeSpace));
        
        if ($usagePercentage > 90) {
            $this->issues[] = "Storage space is running low";
        } elseif ($usagePercentage > 80) {
            $this->warnings[] = "Storage space is getting low";
        }
    }
    
    private function displayResults($executionTime)
    {
        $this->info("\n=== Health Check Results ===");
        $this->info("Execution time: {$executionTime}s");
        
        if (empty($this->issues) && empty($this->warnings)) {
            $this->info("🎉 All systems are healthy!");
        } else {
            if (!empty($this->issues)) {
                $this->error("\n❌ Issues found (" . count($this->issues) . "):");
                foreach ($this->issues as $issue) {
                    $this->error("   • {$issue}");
                }
            }
            
            if (!empty($this->warnings)) {
                $this->warn("\n⚠️  Warnings (" . count($this->warnings) . "):");
                foreach ($this->warnings as $warning) {
                    $this->warn("   • {$warning}");
                }
            }
        }
        
        // Log results
        $logMessage = "Health check completed in {$executionTime}s. ";
        $logMessage .= "Issues: " . count($this->issues) . ", Warnings: " . count($this->warnings);
        Log::info($logMessage);
    }
    
    private function fixIssues()
    {
        $this->info("\n=== Attempting to fix issues ===");
        
        foreach ($this->issues as $issue) {
            if (strpos($issue, 'has no database name') !== false) {
                $this->fixMissingDatabase($issue);
            } elseif (strpos($issue, 'database does not exist') !== false) {
                $this->fixMissingDatabase($issue);
            }
        }
    }
    
    private function fixMissingDatabase($issue)
    {
        // Extract client name from issue message
        preg_match('/Client (.*?) /', $issue, $matches);
        if (isset($matches[1])) {
            $clientName = $matches[1];
            $client = Client::where('name', $clientName)->first();
            
            if ($client) {
                $this->info("   Fixing database for client: {$clientName}");
                $databaseService = new DatabaseService();
                $success = $databaseService->createClientDatabase($client);
                
                if ($success) {
                    $this->info("   ✓ Fixed database for client: {$clientName}");
                } else {
                    $this->error("   ✗ Failed to fix database for client: {$clientName}");
                }
            }
        }
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
} 