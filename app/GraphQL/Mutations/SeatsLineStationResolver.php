<?php

namespace App\GraphQL\Mutations;

use App\SeatsLine;
use Illuminate\Support\Facades\DB;

class SeatsLineStationResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function updateRoute($_, array $args)
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

            DB::update("UPDATE `seats_line_stations` SET 
                `distance` = CASE `id` {$cases} END, 
                `duration` = CASE `id` {$cases} END, 
                `order` = CASE `id` {$cases} END
                WHERE `id` in ({$ids})", $params);

            $total = end($args['stations']);

            SeatsLine::where('id', $args['line_id'])
                ->update([
                    'route' => $args['route'], 
                    'distance' => $total['distance'], 
                    'duration' => $total['duration']
                ]);
            
            return ['distance' => $total['distance'], 'duration' => $total['duration']];
            
        } catch (\Exception $e) {
            throw new CustomException('Could not update route');
        }
    }
}
