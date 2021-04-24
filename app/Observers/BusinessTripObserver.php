<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class BusinessTripObserver
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
        Cache::tags(['userTrips', 'userLiveTrips'])->flush();
    }
}
