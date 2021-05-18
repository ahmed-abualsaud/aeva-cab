<?php

namespace App\Observers;

use App\Admin;
use Illuminate\Support\Facades\Cache;

class AdminObserver
{
    /**
     * Handle the admin "updated" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        Cache::forget('admin.'.$admin->id);
    }

    /**
     * Handle the admin "deleted" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {
        Cache::forget('admin.'.$admin->id);
    }
}
