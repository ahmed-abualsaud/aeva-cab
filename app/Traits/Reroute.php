<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

trait Reroute
{
    public static function handleReroute(string $table, array $args)
    {
        try {
            
            $cases = []; $ids = []; $distance = []; $duration = []; $order = [];

            foreach ($args['stations'] as $value) {
                $id = (int) $value['id'];
                $cases[] = "WHEN {$id} then ?";
                $distance[] = $value['distance'];
                $duration[] = $value['duration'];
                $order[] = $value['order'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = array_merge($distance, $duration, $order);
            $params[] = Carbon::now();
            $stations_tbl = Str::singular($table).'_stations';

            DB::update("UPDATE `{$stations_tbl}` SET 
                `distance` = CASE `id` {$cases} END, 
                `duration` = CASE `id` {$cases} END, 
                `order` = CASE `id` {$cases} END, 
                `updated_at` = ? 
                WHERE `id` in ({$ids})", $params);

            $total = end($args['stations']);

            DB::table($table)
                ->where('id', $args['trip_id'])
                ->update([
                    'route' => $args['route'], 
                    'distance' => $total['distance'], 
                    'duration' => $total['duration']
                ]);
            

            return ['distance' => $total['distance'], 'duration' => $total['duration']];
            
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
        }
    }

    
}