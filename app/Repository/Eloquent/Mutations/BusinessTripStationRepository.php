<?php

namespace App\Repository\Eloquent\Mutations;

use App\BusinessTripSubscription;
use App\BusinessTripStation;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\BusinessTripStationRepositoryInterface;

class BusinessTripStationRepository extends BaseRepository implements BusinessTripStationRepositoryInterface
{
    public function __construct(BusinessTripStation $model)
    {
        parent::__construct($model);
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'trip_id', 'state'])->toArray();

        try {
            $station = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.station_not_found'));
        }

        if (array_key_exists('state', $args) && $args['state'] && $args['state'] != $station->state) {
            $input['state'] = $args['state'];
            $updatedStation = $this->model->where('state', $args['state'])
                ->where('trip_id', $args['trip_id'])
                ->where('id', '<>', $station->id)
                ->first();
            if ($updatedStation) $updatedStation->update(['state' => $station->state]);
        }

        $station->update($input);

        return $station;
    }

    public function assignUser(array $args)
    {
        try {
            $data = [
                'trip_id' => $args['trip_id'],
                'user_id' => $args['user_id'],
                'station_id' => $args['station_id'],
                'destination_id' => $args['destination_id'],
                'subscription_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s'),
                'due_date' => date('Y-m-d H:i:s')
            ];
            return BusinessTripSubscription::upsert($data, ['station_id', 'destination_id', 'updated_at']);
        } catch (\Exception $e) {
            throw new CustomException(__('lang.something_went_wrong'));
        }
    }

    public function acceptStation(array $args)
    {
        DB::beginTransaction();
        try {
            $station = $this->model->where('id', $args['station_id'])->firstOrFail();
            $station->update([
                'name' => $args['station_name'],
                'name_ar' => $args['station_name_ar'],
                'state' => 'PICKABLE', 
                'accepted_at' => date('Y-m-d H:i:s')
            ]);
            
            $data =[
                'trip_id' => $args['trip_id'],
                'station_id' => $args['station_id'],
                'user_id' => $station['request_id'],
                'subscription_verified_at' => date('Y-m-d H:i:s'),
                'due_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'), 
                'updated_at' => date('Y-m-d H:i:s')
            ];
            BusinessTripSubscription::upsert($data, ['station_id', 'updated_at']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.accept_station_failed'));
        }

        return $station;
    }

    public function destroy(array $args)
    {
        try {
            $station = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.station_not_found'));
        }

        /*
        * Revert Business Request

        if ($station->request_type)
            $station->request_type::restore($station->request_id);
        */
        
        $station->delete();

        return $station;
    }
}