<?php

namespace App\Observers;

use App\Manager;
use Illuminate\Support\Facades\Cache;

class ManagerObserver
{
    /**
     * Handle the manager "updated" event.
     *
     * @param  \App\Manager  $manager
     * @return void
     */
    public function updated(Manager $manager)
    {
        Cache::forget('manager.'.$manager->id);
    }

    /**
     * Handle the manager "deleted" event.
     *
     * @param  \App\Manager  $manager
     * @return void
     */
    public function deleted(Manager $manager)
    {
        Cache::forget('manager.'.$manager->id);
    }
}
