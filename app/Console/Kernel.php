<?php

namespace App\Console;

use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Log;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('database:backup')->cron('0 0 */3 * *')->at('01:00')
            ->onSuccess(function (Stringable $output) {
                Log::info('Database backup executed successfully!  output: '.$output);
            })
            ->onFailure(function (Stringable $output) {
                Log::error('Database backup failed with error: '.$output);
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
