<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('model:prune')->daily();
        $schedule->command('app:check-urgent-alerts')->dailyAt('08:00');
        $schedule->command('app:backup-db')->dailyAt('01:00');
        $schedule->command('app:clean-payment-proofs')->daily();
        
        // Kirim Excel Laporan Mingguan tiap hari Minggu jam 01:30
        $schedule->command('app:send-weekly-backup')->weeklyOn(0, '01:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
