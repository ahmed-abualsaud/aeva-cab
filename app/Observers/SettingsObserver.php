<?php

namespace App\Observers;

use App\Jobs\DriverScanAndPayCashback;
use App\Settings;
use Illuminate\Support\Facades\Cache;

class SettingsObserver
{
    /**
     * @param Settings $settings
     * @return void
     */
    public function updated(Settings $settings)
    {
        DriverScanAndPayCashback::cacheSettings();
    }

}
