<?php

namespace App\Console;

use App\Console\Commands\ChangeDriverStatus;
use App\Driver;
use App\Events\AllDriversLocations;

use Carbon\Carbon;

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
        ChangeDriverStatus::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('database:backup')
        //     ->cron('0 0 */3 * *')
        //     ->at('01:00')
        //     ->onSuccess(function (Stringable $output) {
        //         Log::info('Database backup executed successfully!  output: '.$output);
        //     })
        //     ->onFailure(function (Stringable $output) {
        //         Log::error('Database backup failed with error: '.$output);
        //     });

        $schedule->call(function () {
                $dt = Carbon::now();
                $x=60/10;
                do{
                    $locations = Driver::select('full_name', 'phone', 'latitude', 'longitude')->get()->toArray();
                    broadcast(new AllDriversLocations($locations));
                    time_sleep_until($dt->addSeconds(10)->timestamp);
                } while($x-- > 1);
            })
            ->everyMinute()
            ->onFailure(function (Stringable $output) {
                Log::error('Broadcast all drivers locations failed with error: '.$output);
            });

        $schedule->command('change:driver-status')->everyFifteenMinutes();
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
