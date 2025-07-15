<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CleanupBackups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vergeflow:cleanup-backups 
                            {--days=30 : Keep backups for this many days}
                            {--path= : Custom backup path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old backup files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupPath = $this->option('path') ?: storage_path('backups');
        $daysToKeep = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        $this->info("Cleaning up backups older than {$daysToKeep} days...");
        $this->info("Backup path: {$backupPath}");
        
        if (!File::exists($backupPath)) {
            $this->warn("Backup directory does not exist: {$backupPath}");
            return;
        }
        
        $files = File::files($backupPath);
        $deletedCount = 0;
        $deletedSize = 0;
        
        foreach ($files as $file) {
            $fileDate = Carbon::createFromTimestamp($file->getMTime());
            
            if ($fileDate->lt($cutoffDate)) {
                $size = $file->getSize();
                $deletedSize += $size;
                
                File::delete($file->getPathname());
                $this->info("Deleted: {$file->getFilename()} (" . $this->formatBytes($size) . ")");
                $deletedCount++;
            }
        }
        
        $this->info("Cleanup completed:");
        $this->info("  - Files deleted: {$deletedCount}");
        $this->info("  - Space freed: " . $this->formatBytes($deletedSize));
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