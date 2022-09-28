<?php

namespace Database\Seeders;

use App\Settings;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DriverScanAndPayCashbackSettingsSeeder extends Seeder
{
    protected string $max_amount_key = 'Driver Scan And Pay Cashback Max Amount';
    protected string $percent_key = 'Driver Scan And Pay Cashback Percent';
    protected string $daily_at_key = 'Driver Scan And Pay Cashback Daily At';
    protected string $transactions_per_queue_key = 'Driver Scan And Pay Cashback Transactions Per Queue';
    protected string $activation_key = 'Activated Driver Scan And Pay Cashback Cron Job';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //max amount
       Settings::updateOrCreate(['name'=> $this->max_amount_key],[
            'name'=> $this->max_amount_key,
            'value'=> custom_number(200),
            'description'=> 'max amount given for driver as a scan and pay cashback',
       ]);

        //percent
       Settings::updateOrCreate(['name'=> $this->percent_key],[
            'name'=> $this->percent_key,
            'value'=> custom_number( 10/100),
            'description'=> 'percent given for driver as a scan and pay cashback',
       ]);

        //daily_at
        Settings::updateOrCreate(['name'=> $this->daily_at_key],[
            'name'=> $this->daily_at_key,
            'value'=> "04:00",
            'description'=> 'time that will scan and pay cashback cron job run at',
       ]);

        //activation
        Settings::updateOrCreate(['name'=> $this->activation_key],[
            'name'=> $this->activation_key,
            'value'=> "on",
            'description'=> 'time that will scan and pay cashback cron job run at',
       ]);

        //transactions per queue
        Settings::updateOrCreate(['name'=> $this->transactions_per_queue_key],[
            'name'=> $this->transactions_per_queue_key,
            'value'=> 500,
            'description'=> 'number of transactions per queue',
       ]);
    }
}
