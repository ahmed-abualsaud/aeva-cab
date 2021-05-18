<?php

namespace App\Observers;

use App\Driver;
use Illuminate\Support\Facades\Cache;

class DriverObserver
{
    /**
     * Handle the driver "deleted" event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function deleted(Driver $driver)
    {
        Cache::forget('driver.'.$driver->id);
    }
}
