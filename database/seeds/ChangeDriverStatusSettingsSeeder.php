<?php

namespace Database\Seeders;

use App\Settings;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ChangeDriverStatusSettingsSeeder extends Seeder
{
    protected string $duration_key = 'Acceptable Online Duration';
    protected string $activation_key = 'Activated Driver Status Cron Job';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       Settings::updateOrCreate(['name'=> $this->duration_key],[
            'name'=> $this->duration_key,
            'value'=> 15,
            'description'=> 'acceptable duration in minutes that driver can be online after last location update',
       ]);

        Settings::updateOrCreate(['name'=> $this->activation_key],[
            'name'=> $this->activation_key,
            'value'=> 'on',
            'description'=> 'acceptable distance in meter that driver can be online within last location update',
       ]);
    }
}
