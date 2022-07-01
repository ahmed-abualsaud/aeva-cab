<?php

namespace Database\Seeders;

use App\Driver;
use App\DriverStats;

use Illuminate\Database\Seeder;

class DriverStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ids = Driver::select('id')->get();
        $ids = array_map(function (array $arr) {
            foreach ($arr as $key => $value) {
                $arr['driver_id'] = $value;
            }
            unset($arr['id']);
            return $arr;
        }, $ids->toArray());

        if($ids) {
            DriverStats::insert($ids);
        }
    }
}
