<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsLine;
use App\SeatsLineStation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\SeatsLineRepositoryInterface;

class SeatsLineRepository extends BaseRepository implements SeatsLineRepositoryInterface
{
    public function __construct(SeatsLine $model)
    {
        parent::__construct($model);
    }

    public function copy(array $args)
    {
        DB::beginTransaction();
        try {
            $line = $this->createLineCopy($args);
            $this->createStationsCopy($args['id'], $line->id);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.copy_line_failed'));
        }

        return $line;
    }

    public function updateRoute(array $args)
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

            $this->model->where('id', $args['line_id'])
                ->update([
                    'route' => $args['route'], 
                    'distance' => $total['distance'], 
                    'duration' => $total['duration']
                ]);
            
            return ['distance' => $total['distance'], 'duration' => $total['duration']];
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_route_failed'));
        }
    }

    protected function createLineCopy(array $args)
    {
        $originalLine = $this->model->select(
            'partner_id', 'city_id', 'code', 'distance', 'duration', 'base_price', 'distance_price', 'minimum_distance', 'route'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalLine['name'] = $args['name'];
        $originalLine['name_ar'] = $args['name_ar'];
        
        return $this->model->create($originalLine);
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
        }

        return SeatsLineStation::insert($originalStations->toArray());
    }
}
