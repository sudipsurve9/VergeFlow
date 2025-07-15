<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OptimizeSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:optimize-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize VergeFlow system for production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VergeFlow System Optimization ===');
        
        // Clear all caches
        $this->info('1. Clearing caches...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('   ✓ Caches cleared');
        
        // Rebuild caches for production
        $this->info('2. Rebuilding caches...');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $this->info('   ✓ Caches rebuilt');
        
        // Optimize autoloader
        $this->info('3. Optimizing autoloader...');
        $this->executeCommand('composer dump-autoload --optimize --no-dev');
        $this->info('   ✓ Autoloader optimized');
        
        // Run database optimizations
        $this->optimizeDatabases();
        
        // Clear old logs
        $this->clearOldLogs();
        
        $this->info('=== System optimization completed ===');
    }
    
    private function optimizeDatabases()
    {
        $this->info('4. Optimizing databases...');
        
        // Optimize main database
        try {
            DB::statement('OPTIMIZE TABLE clients, users, migrations');
            $this->info('   ✓ Main database optimized');
        } catch (\Exception $e) {
            $this->warn("   ⚠ Main database optimization failed: " . $e->getMessage());
        }
        
        // Optimize client databases
        $clients = \App\Models\Client::whereNotNull('database_name')->get();
        
        foreach ($clients as $client) {
            try {
                $databaseService = new \App\Services\DatabaseService();
                $connection = $databaseService->getClientConnection($client);
                
                DB::connection($connection)->statement('OPTIMIZE TABLE products, orders, users, categories');
                $this->info("   ✓ Client {$client->name} database optimized");
                
            } catch (\Exception $e) {
                $this->warn("   ⚠ Client {$client->name} optimization failed: " . $e->getMessage());
            }
        }
    }
    
    private function clearOldLogs()
    {
        $this->info('5. Clearing old logs...');
        
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        $deletedCount = 0;
        
        foreach ($files as $file) {
            $fileAge = time() - filemtime($file);
            $daysOld = $fileAge / (24 * 60 * 60);
            
            // Delete logs older than 30 days
            if ($daysOld > 30) {
                unlink($file);
                $deletedCount++;
            }
        }
        
        $this->info("   ✓ Deleted {$deletedCount} old log files");
    }
    
    private function executeCommand($command)
    {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->warn("Command failed: " . implode("\n", $output));
        }
    }
} 