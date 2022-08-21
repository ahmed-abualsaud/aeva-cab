<?php

namespace Database\Seeders;

use Aeva\Cab\Domain\Models\Trace;
use App\Driver;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TracesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $driver = Driver::query()->select('id as guard_id','longitude','latitude')->latest()->first();
        Trace::create([
            'guard' => 'driver',
            'guard_id' => $driver['guard_id'],
            'event' => 'login',
            'longitude' => $driver['longitude'],
            'latitude' => $driver['latitude'],
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

    }

}
