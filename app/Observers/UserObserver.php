<?php

namespace App\Observers;

use App\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        Cache::forget('user.'.$user->id);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        Cache::forget('user.'.$user->id);
    }
}
