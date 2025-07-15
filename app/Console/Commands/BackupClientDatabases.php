<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupClientDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:backup-databases 
                            {--client-id= : Backup specific client by ID}
                            {--all : Backup all client databases}
                            {--main : Backup main database only}
                            {--path= : Custom backup path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup client databases for production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupPath = $this->option('path') ?: storage_path('backups');
        
        // Create backup directory if it doesn't exist
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        
        if ($this->option('main')) {
            $this->backupMainDatabase($backupPath, $timestamp);
        } elseif ($this->option('client-id')) {
            $this->backupSpecificClient($this->option('client-id'), $backupPath, $timestamp);
        } elseif ($this->option('all')) {
            $this->backupAllDatabases($backupPath, $timestamp);
        } else {
            $this->error('Please specify --main, --all, or --client-id option');
            return 1;
        }
    }

    private function backupMainDatabase($backupPath, $timestamp)
    {
        $this->info('Backing up main database...');
        
        $config = config('database.connections.mysql');
        $filename = "vergeflow_main_{$timestamp}.sql";
        $filepath = "{$backupPath}/{$filename}";
        
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s',
            $config['host'],
            $config['port'],
            $config['username'],
            $config['password'],
            $config['database'],
            $filepath
        );
        
        $this->executeBackup($command, $filepath, 'main database');
    }

    private function backupSpecificClient($clientId, $backupPath, $timestamp)
    {
        $client = Client::find($clientId);
        if (!$client) {
            $this->error("Client with ID {$clientId} not found.");
            return;
        }

        if (!$client->database_name) {
            $this->warn("Client {$client->name} has no database to backup.");
            return;
        }

        $this->backupClientDatabase($client, $backupPath, $timestamp);
    }

    private function backupAllDatabases($backupPath, $timestamp)
    {
        $this->info('Backing up all databases...');
        
        // Backup main database
        $this->backupMainDatabase($backupPath, $timestamp);
        
        // Backup all client databases
        $clients = Client::whereNotNull('database_name')->get();
        $this->info("Found {$clients->count()} client databases to backup.");
        
        foreach ($clients as $client) {
            $this->backupClientDatabase($client, $backupPath, $timestamp);
        }
    }

    private function backupClientDatabase($client, $backupPath, $timestamp)
    {
        $this->info("Backing up client: {$client->name}");
        
        $config = config('database.connections.mysql');
        $filename = "{$client->database_name}_{$timestamp}.sql";
        $filepath = "{$backupPath}/{$filename}";
        
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s',
            $config['host'],
            $config['port'],
            $config['username'],
            $config['password'],
            $client->database_name,
            $filepath
        );
        
        $this->executeBackup($command, $filepath, "client {$client->name}");
    }

    private function executeBackup($command, $filepath, $description)
    {
        $this->info("  Executing backup for {$description}...");
        
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($filepath)) {
            $size = filesize($filepath);
            $this->info("  ✓ Backup completed: {$filepath} (" . $this->formatBytes($size) . ")");
            
            // Log the backup
            Log::info("Database backup completed: {$filepath} ({$description})");
        } else {
            $this->error("  ✗ Backup failed for {$description}");
            $this->error("  Command output: " . implode("\n", $output));
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