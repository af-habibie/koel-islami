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
        Commands\SyncMedia::class,
        Commands\Init::class,
        Commands\GenerateJWTSecret::class,
        Commands\Apiinsert::class,
        Commands\ComAdzan::class,
        Commands\ComAudio::class,
        Commands\CronHadist::class,
        Commands\RandTimeHadist::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('play:adzan')->everyMinute();
        //$schedule->command('play:hadist')->everyMinute();
	    $schedule->command('play:audio')->everyMinute();
        $schedule->command('hadist:rand')->everyMinute();
        $schedule->command('play:hadist')->dailyAt('02:00');
        $schedule->command('updateapi')->dailyAt('02:00');
    }
}
