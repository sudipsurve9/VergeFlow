<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class DebugDatabaseCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:debug-db-creation {--client-id= : Debug specific client by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug database creation issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VergeFlow Database Creation Debug ===');
        
        // Step 1: Check database configuration
        $this->checkDatabaseConfig();
        
        // Step 2: Test MySQL connection
        $this->testMySQLConnection();
        
        // Step 3: Check MySQL permissions
        $this->checkMySQLPermissions();
        
        // Step 4: Test database creation manually
        $this->testDatabaseCreation();
        
        // Step 5: Check specific client if provided
        if ($clientId = $this->option('client-id')) {
            $this->debugSpecificClient($clientId);
        }
        
        $this->info('=== Debug Complete ===');
    }
    
    private function checkDatabaseConfig()
    {
        $this->info('1. Checking database configuration...');
        
        $config = config('database.connections.mysql');
        $this->info("   Host: {$config['host']}");
        $this->info("   Port: {$config['port']}");
        $this->info("   Database: {$config['database']}");
        $this->info("   Username: {$config['username']}");
        $this->info("   Password: " . (empty($config['password']) ? 'empty' : 'set'));
        
        // Check if we can connect to the main database
        try {
            DB::connection('mysql')->getPdo();
            $this->info('   ✓ Main database connection successful');
        } catch (\Exception $e) {
            $this->error("   ✗ Main database connection failed: " . $e->getMessage());
        }
    }
    
    private function testMySQLConnection()
    {
        $this->info('2. Testing MySQL connection without database...');
        
        $config = config('database.connections.mysql');
        
        try {
            $pdo = new \PDO(
                "mysql:host={$config['host']};port={$config['port']}",
                $config['username'],
                $config['password']
            );
            $this->info('   ✓ MySQL connection successful');
            
            // Test if we can see existing databases
            $stmt = $pdo->query('SHOW DATABASES');
            $databases = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            $this->info("   ✓ Found " . count($databases) . " databases");
            
            // Show vergeflow databases
            $vergeflowDbs = array_filter($databases, function($db) {
                return strpos($db, 'vergeflow') === 0;
            });
            if (!empty($vergeflowDbs)) {
                $this->info("   ✓ VergeFlow databases: " . implode(', ', $vergeflowDbs));
            } else {
                $this->info("   ⚠ No VergeFlow databases found");
            }
            
        } catch (\Exception $e) {
            $this->error("   ✗ MySQL connection failed: " . $e->getMessage());
        }
    }
    
    private function checkMySQLPermissions()
    {
        $this->info('3. Checking MySQL permissions...');
        
        $config = config('database.connections.mysql');
        
        try {
            $pdo = new \PDO(
                "mysql:host={$config['host']};port={$config['port']}",
                $config['username'],
                $config['password']
            );
            
            // Check user privileges
            $stmt = $pdo->query("SHOW GRANTS FOR CURRENT_USER()");
            $grants = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            $hasCreatePrivilege = false;
            $hasAllPrivileges = false;
            
            foreach ($grants as $grant) {
                if (strpos($grant, 'CREATE') !== false) {
                    $hasCreatePrivilege = true;
                }
                if (strpos($grant, 'ALL PRIVILEGES') !== false) {
                    $hasAllPrivileges = true;
                }
            }
            
            if ($hasAllPrivileges) {
                $this->info('   ✓ User has ALL PRIVILEGES');
            } elseif ($hasCreatePrivilege) {
                $this->info('   ✓ User has CREATE privilege');
            } else {
                $this->error('   ✗ User lacks CREATE privilege');
                $this->warn('   Run: GRANT ALL PRIVILEGES ON *.* TO \'root\'@\'localhost\' WITH GRANT OPTION;');
            }
            
        } catch (\Exception $e) {
            $this->error("   ✗ Failed to check permissions: " . $e->getMessage());
        }
    }
    
    private function testDatabaseCreation()
    {
        $this->info('4. Testing manual database creation...');
        
        $config = config('database.connections.mysql');
        $testDbName = 'vergeflow_test_' . time();
        
        try {
            $pdo = new \PDO(
                "mysql:host={$config['host']};port={$config['port']}",
                $config['username'],
                $config['password']
            );
            
            // Try to create a test database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$testDbName}`");
            $this->info("   ✓ Successfully created test database: {$testDbName}");
            
            // Try to drop the test database
            $pdo->exec("DROP DATABASE IF EXISTS `{$testDbName}`");
            $this->info("   ✓ Successfully dropped test database: {$testDbName}");
            
        } catch (\Exception $e) {
            $this->error("   ✗ Failed to create test database: " . $e->getMessage());
        }
    }
    
    private function debugSpecificClient($clientId)
    {
        $this->info("5. Debugging client ID: {$clientId}");
        
        $client = Client::find($clientId);
        if (!$client) {
            $this->error("   ✗ Client not found");
            return;
        }
        
        $this->info("   Client: {$client->name}");
        $this->info("   Database name: " . ($client->database_name ?: 'not set'));
        
        if ($client->database_name) {
            // Check if database exists
            $config = config('database.connections.mysql');
            try {
                $pdo = new \PDO(
                    "mysql:host={$config['host']};port={$config['port']}",
                    $config['username'],
                    $config['password']
                );
                
                $stmt = $pdo->query("SHOW DATABASES LIKE '{$client->database_name}'");
                $exists = $stmt->rowCount() > 0;
                
                if ($exists) {
                    $this->info("   ✓ Database exists: {$client->database_name}");
                } else {
                    $this->warn("   ⚠ Database doesn't exist: {$client->database_name}");
                }
                
            } catch (\Exception $e) {
                $this->error("   ✗ Failed to check database: " . $e->getMessage());
            }
        }
        
        // Try to generate database name
        $databaseName = 'vergeflow_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $client->name)) . '_' . $client->id;
        $this->info("   Generated name: {$databaseName}");
    }
} 