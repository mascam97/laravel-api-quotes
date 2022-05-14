<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Just a task as example
        $schedule->call(function () {
            echo 'Test';
        })->sendOutputTo(storage_path('logs/test_schedule.log'))
            ->everyMinute()
            ->evenInMaintenanceMode();

        // Task to refresh the database each month
        $schedule->command('migrate:refresh --seed')
            ->sendOutputTo(storage_path('logs/refresh_database.log'))
            ->monthly()
            ->onSuccess(function () {
                Log::info('The command to refresh the database worked');
            })
            ->onFailure(function () {
                Log::error('The command to refresh the database did not work');
            });

        // Task to sent a weekly report about how many users and quotes are
        $schedule->command('send:newsletter --schedule')
            ->sendOutputTo(storage_path('logs/send_newsletter_schedule.log'))
            ->onOneServer()
            ->withoutOverlapping()
            ->weekly()
            ->onSuccess(function () {
                Log::info('The command to refresh the database worked');
            })
            ->onFailure(function () {
                Log::error('The command to sent email to users did not work');
            });
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
