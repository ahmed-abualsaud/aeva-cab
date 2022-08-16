<?php

namespace Database\Seeders;

use App\Settings;

use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::insert([
            [
                'name' => 'Waiting Time',
                'value' => 360,
                'description' => 'driver waiting time in seconds'
            ],
            [
                'name' => 'Cancelation Fees',
                'value' => 10,
                'description' => 'the amount of cancelation fees in egyption pound'
            ],
            [
                'name' => 'Pickup Area Distance',
                'value' => 10,
                'description' => 'the minimum distance in meters that the driver should notifies the user of his arrival at the pickup location'
            ],
            [
                'name' => 'Coverage Radius',
                'value' => 500000,
                'description' => 'search availability radius in meters'
            ],
            [
                'name' => 'Search Radius',
                'value' => 500000,
                'description' => 'search radius in meters'
            ],
            [
                'name' => 'Referral Count',
                'value' => 20,
                'description' => 'the number of rides a driver should complete to add th referral bonus to the driver\'s balance that referred him'
            ],
            [
                'name' => 'Referral Bonus',
                'value' => 100,
                'description' => 'the amount of the referral bonus in egyption pound'
            ],
            [
                'name' => 'Coverage Center Latitude',
                'value' => 31.200182160716306,
                'description' => 'coverage center latitude'
            ],
            [
                'name' => 'Coverage Center Longitude',
                'value' => 29.918740737703292,
                'description' => 'coverage center longitude'
            ],
            [
                'name' => 'Show Acceptance Dialog',
                'value' => 30,
                'description' => 'show acceptance dialog duration in seconds for the driver'
            ],
            [
                'name' => 'Cashout Amount Limit',
                'value' => 200,
                'description' => 'restrict the cashout amount a driver can withdraw per day'
            ]
        ]);
    }
}
