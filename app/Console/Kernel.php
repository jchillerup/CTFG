<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $schedule->command('sync:tables')
            ->hourly()
            ->timezone('UTC');

        $schedule->command('render-cache:category')
            ->hourly()
            ->timezone('America/New_York')
            ->after(function () {
                // Run 5 minutes after sync to ensure data is available
            });

        $schedule->command('render-cache:tag')
            ->hourly()
            ->timezone('America/New_York')
            ->after(function () {
                // Run 5 minutes after sync to ensure data is available
            });

        $environment = App::environment();
        if ($environment == "production") {
            $schedule->command('health-checker')
                ->weekly()
                ->timezone('America/New_York');

            $schedule->command('confs:update')
                ->everySixHours();
        }

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
