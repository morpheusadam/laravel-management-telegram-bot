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
        'App\Console\Commands\CronCall',
        'App\Http\Package\LaravelTranslation\MissingTranslations',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call('App\Http\Controllers\Cron@telegram_download_avatar')->everyTwoMinutes();
        $schedule->call('App\Http\Controllers\Cron@telegram_broadcast_send')->everyMinute();
        $schedule->call('App\Http\Controllers\Cron@telegram_broadcast_sequence_hourly')->everyTwoMinutes();
        $schedule->call('App\Http\Controllers\Cron@telegram_broadcast_sequence_daily')->everyFiveMinutes();
        $schedule->call('App\Http\Controllers\Cron@telegram_disable_bot_expired_users')->daily();
        $schedule->call('App\Http\Controllers\Cron@telegram_clean_junk_data')->weekly();
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
