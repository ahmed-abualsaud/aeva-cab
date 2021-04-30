<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class SeatsTripObserver
{
    public function created()
    {
        $this->cacheFlush();
    }
    
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
