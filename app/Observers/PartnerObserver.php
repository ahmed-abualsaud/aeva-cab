<?php

namespace App\Observers;

use App\Partner;
use Illuminate\Support\Facades\Cache;

class PartnerObserver
{
    /**
     * Handle the partner "deleted" event.
     *
     * @param  \App\Partner  $partner
     * @return void
     */
    public function deleted(Partner $partner)
    {
        Cache::forget('partner.'.$partner->id);
    }
}
