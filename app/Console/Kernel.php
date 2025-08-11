<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\MigrateToMultiTenant::class,
        Commands\SetupClientDatabases::class,
        Commands\FixClientDatabaseColumns::class,
        Commands\CleanupMainDatabase::class,
        Commands\RemoveClientTablesFromMain::class,
        Commands\TestSuperAdminPortal::class,
        Commands\CheckClients::class,
        Commands\CheckClientDatabase::class,
        Commands\CheckTableStructure::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Daily health check at 2 AM
        $schedule->command('vergeflow:health-check')->dailyAt('02:00');
        
        // Daily database backup at 3 AM
        $schedule->command('vergeflow:backup-databases --all')->dailyAt('03:00');
        
        // Weekly cleanup of old backups (keep last 30 days)
        $schedule->command('vergeflow:cleanup-backups')->weekly();
        
        // Monthly system optimization
        $schedule->command('vergeflow:optimize-system')->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
