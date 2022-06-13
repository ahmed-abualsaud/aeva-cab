<?php

use Illuminate\Database\Seeder;

use Database\Seeders\SettingsSeeder;
use Database\Seeders\DriverStatsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SettingsSeeder::class,
            DriverStatsSeeder::class
        ]);
        // $this->call(UsersTableSeeder::class);
    }
}
