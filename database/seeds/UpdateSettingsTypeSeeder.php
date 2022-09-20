<?php

namespace Database\Seeders;

use App\Settings;

use Illuminate\Database\Seeder;

class UpdateSettingsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $float = [
            "Cancelation Fees",
            "Pickup Area Distance",
            "Coverage Radius",
            "Search Radius",
            "Referral Bonus",
            "Coverage Center Latitude",
            "Coverage Center Longitude",
            "Cashout Amount Limit",
            "Driver Scan And Pay Cashback Max Amount",
            "Driver Scan And Pay Cashback Percent",
        ];

        $bool = [
            "Activated Driver Status Cron Job",
            "Activated Driver Scan And Pay Cashback Cron Job",
        ];

        $int = [
            "Waiting Time",
            "Driver Scan And Pay Cashback Transactions Per Queue",
            "Referral Count",
            "Show Acceptance Dialog",
            "Acceptable Online Duration",
        ];

        $time = [
            "Driver Scan And Pay Cashback Daily At",
        ];

        collect(compact('float','bool','int','time'))->each(fn($names,$type) => Settings::query()->whereIn('name',$names)->update(compact('type')));
    }
}
