<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsTrip;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

class SeatsTripRepository extends BaseRepository
{
    public function __construct(SeatsTrip $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($args, ['directive']);
            $trip = $this->createTrip($input);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.create_trip_failed'));
        }

        return $trip;
    }

    public function update(array $args)
    {
        try {
            $tripInput = Arr::except($args, ['directive']);
            $trip = $this->model->findOrFail($args['id']);
            $trip->update($tripInput);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.trip_not_found'));
        }

        return $trip;
    }

    public function copy(array $args)
    {
        DB::beginTransaction();
        try {
            $trip = $this->createTripCopy($args);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.copy_trip_failed'));
        }

        return $trip;
    }

    protected function createTrip($input)
    {
        return $this->model->create($input);
    }

    protected function createTripCopy(array $args)
    {
        $originalTrip = $this->model->select(
            'line_id', 'partner_id', 'driver_id', 'vehicle_id', 'start_date', 'end_date', 
            'days', 'bookable', 'price'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalTrip['name'] = $args['name'];
        $originalTrip['name_ar'] = $args['name_ar'];
        
        return $this->createTrip($originalTrip);
    }
}
