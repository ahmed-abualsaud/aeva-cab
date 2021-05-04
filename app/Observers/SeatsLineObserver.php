<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class SeatsLineObserver
{
    public function updated()
    {
        $this->cacheFlush();
    }

    public function deleted()
    {
        $this->cacheFlush();
    }

    protected function cacheFlush()
    {
        Cache::tags('seatsNearbyStations')->flush();
    }
}
