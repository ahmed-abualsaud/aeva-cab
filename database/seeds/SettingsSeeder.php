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
                'name' => 'Aevapay Server Key',
                'value' => '$2y$10$PoO5Gfl4PAezsMeI0LPbKul5Kes4Ee06pIGGsMVV36Zy6BXne/Lom',
                'description' => 'aevapay staging server key'
            ],
            [
                'name' => 'Aevapay Staging Server',
                'value' => 'staging.aevapay.net/api/v1/aevacab-in',
                'description' => 'aevapay staging server domain name'
            ],
            [
                'name' => 'Aevapay Production Server',
                'value' => 'production.aevapay.net/api/v1/aevacab-in',
                'description' => 'aevapay production server domain name'
            ],
            [
                'name' => 'Aeva Mobility Server Key',
                'value' => '!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ!K4O^Coj_y3baShe4L7;Rhp]C)y6yiacx+Tn.^%xVev9t4Jd`7D`gVG+3>HdTrJ',
                'description' => 'aeva mobility server key'
            ]
        ]);
    }
}
