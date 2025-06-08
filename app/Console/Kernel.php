<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        \App\Console\Commands\RefreshCampaignContents::class,
    ];


    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('sales:create')->dailyAt('00:01')->withoutOverlapping();
        // $schedule->command('marketing:create')->dailyAt('00:02')->withoutOverlapping();
        // $schedule->command('data:scrap')->dailyAt('04:00');
        // $schedule->command('data:scrap-contest')->dailyAt('05:00');
        // $schedule->command('statistic:campaign-recap')->dailyAt('05:30');
        // // $schedule->command('orders:fetch-external')->cron('0 9,12,17,19,21,3,6 * * *')->timezone('Asia/Jakarta');
        // $schedule->command('attendance:populate')->dailyAt('00:05');
        // $schedule->command('campaign:refresh-contents')->dailyAt('03:00');
        // $schedule->command('update:report-count')->dailyAt('09:58');
        // $schedule->command('report:send-telegram')->dailyAt('10:00');
        // $schedule->command('google-sheet:import')->dailyAt('14:30');
        // $schedule->command('import:visit')->dailyAt('14:00')
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