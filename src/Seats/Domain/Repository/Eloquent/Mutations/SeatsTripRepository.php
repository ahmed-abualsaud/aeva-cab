<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Mutations;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

use App\Exceptions\CustomException;

use Qruz\Seats\Domain\Models\SeatsTrip;
use Qruz\Seats\Domain\Repository\Eloquent\BaseRepository;

class SeatsTripRepository extends BaseRepository
{
    public function __construct(SeatsTrip $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create(array $args)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($args, ['directive']);
            $trip = $this->createTrip($input);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException($e->getMessage());
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
            'days', 'bookable', 'ac', 'base_price', 'distance_price', 'minimum_distance'
            )
            ->findOrFail($args['id'])
            ->toArray();

        $originalTrip['name'] = $args['name'];
        $originalTrip['name_ar'] = $args['name_ar'];
        
        return $this->createTrip($originalTrip);
    }
}
