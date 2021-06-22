<?php

namespace App\GraphQL\Mutations;

use App\SeatsLine;
use App\SeatsLineStation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class SeatsLineResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */

    public function copy($_, array $args)
    {
        DB::beginTransaction();
        try {
            $line = $this->createLineCopy($args);
            $this->createStationsCopy($args['id'], $line->id);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException('We could not able to copy this line!');
        }

        return $line;
    }

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

    protected function createLineCopy(array $args)
    {
        $originalLine = SeatsLine::select(
            'partner_id', 'code', 'distance', 'duration', 'price', 'route'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalLine['name'] = $args['name'];
        $originalLine['name_ar'] = $args['name_ar'];
        
        return SeatsLine::create($originalLine);
    }

    protected function createStationsCopy($oldLineId, $newLineId)
    {
        $originalStations = SeatsLineStation::select(
            'name', 'name_ar', 'latitude', 'longitude', 'duration', 'distance', 'state', 'order'
            )
            ->where('line_id', $oldLineId)
            ->get();

        foreach($originalStations as $station) {
            $station->line_id = $newLineId;
            $station->created_at = now();
            $station->updated_at = now();
        }

        return SeatsLineStation::insert($originalStations->toArray());
    }
}
