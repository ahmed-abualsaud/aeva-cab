<?php

namespace App\Repository\Eloquent\Mutations;

use App\BusinessTrip;
use App\BusinessTripSubscription;
use Illuminate\Support\Arr;
use App\BusinessTripStation;
use App\BusinessTripSchedule;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Vinkla\Hashids\Facades\Hashids;
use App\Repository\Mutations\BusinessTripRequestRepositoryInterface;

class BusinessTripRequestRepository implements BusinessTripRequestRepositoryInterface
{
    private $businessTrip;
    private $businessTripSubscription;
    private $businessTripStation;
    private $businessTripSchedule;

    public function __construct(BusinessTrip $businessTrip, 
    BusinessTripSubscription $businessTripSubscription,
    BusinessTripStation $businessTripStation, BusinessTripSchedule $businessTripSchedule)
    {
        $this->businessTrip = $businessTrip;
        $this->businessTripSubscription = $businessTripSubscription;
        $this->businessTripStation = $businessTripStation;
        $this->businessTripSchedule = $businessTripSchedule;
    }

    public function createTrip(array $args)
    {
        DB::beginTransaction();
        try {
            $input = Arr::except($args, ['directive', 'request_ids', 'destinations', 'users', 'request_type']);
            $businessTrip = $this->createBusinessTrip($input);
            $this->createStationsAndDestinations($args, $businessTrip->id);
            $this->assignUsersToStationsAndDestinations($args, $businessTrip->id);
            $this->createScheduleForEachUser($args['users'], $businessTrip->id);
            $args['request_type']::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.create_trip_failed'));
        }
    }

    public function addToTrip(array $args)
    {
        DB::beginTransaction();
        try {
            if (array_key_exists('station_id', $args) && array_key_exists('destination_id', $args)) {
                $this->assignUsersToStationAndDestination($args);
            } else if (array_key_exists('station_id', $args)) {
                $this->createDestinations($args);
                $this->assignUsersToDestinations($args);
                $this->assignUsersToStation($args);
            } else if (array_key_exists('destination_id', $args)) {
                $this->createStations($args);
                $this->assignUsersToStations($args);
                $this->assignUsersToDestination($args);
            } else {
                $this->createStationsAndDestinations($args, $args['trip_id']);
                $this->assignUsersToStationsAndDestinations($args, $args['trip_id']);
            }

            $this->updateTripSchedule($args);
            $this->createScheduleForEachUser($args['users'], $args['trip_id']);
            $args['request_type']::accept($args['request_ids']);

            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.add_request_failed'));
        }
    }

    protected function assignUsersToStationAndDestination(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            $this->businessTripSubscription->upsert(
                $data, ['station_id', 'destination_id', 'request_type', 'request_id']
            );

        } catch(\Exception $e) {
            throw new CustomException(__('lang.assign_user_station_failed'));
        }
    }

    protected function assignUsersToStation(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            $this->businessTripSubscription->upsert(
                $data, ['station_id', 'request_type', 'request_id']
            );

        } catch(\Exception $e) {
            throw new CustomException(__('lang.assign_user_station_failed'));
        }
    }

    protected function assignUsersToDestination(array $args)
    {
        try {
            
            $data = $this->stationsData($args);

            $this->businessTripSubscription->upsert(
                $data, ['destination_id', 'request_type', 'request_id']
            );

        } catch(\Exception $e) {
            throw new CustomException(__('lang.assign_user_station_failed'));
        }
    }

    protected function assignUsersToStationsAndDestinations(array $args, int $trip_id)
    {
        $arr = $this->subscriptionData($args, $trip_id);

        $stations = $this->stationsByTrip($trip_id);

        foreach($args['users'] as $user) {
            $arr['user_id'] = $user['id'];
            $arr['station_id'] = $stations->firstWhere('request_id', $user['request_id'])->id;
            $arr['destination_id'] = $stations->firstWhere('name', $user['destination'])->id;
            $arr['request_id'] = $user['request_id'];
            $data[] = $arr;
        }

        $this->businessTripSubscription->insert($data);
    }

    protected function assignUsersToStations(array $args)
    {
        $arr = $this->subscriptionData($args, $args['trip_id']);

        $stations = $this->stationsByTrip($args['trip_id']);

        foreach($args['users'] as $user) {
            $arr['user_id'] = $user['id'];
            $arr['station_id'] = $stations->firstWhere('request_id', $user['request_id'])->id;
            $arr['request_id'] = $user['request_id'];
            $data[] = $arr;
        }

        $this->businessTripSubscription->insert($data);
    }

    protected function assignUsersToDestinations(array $args)
    {
        $arr = $this->subscriptionData($args, $args['trip_id']);

        $stations = $this->stationsByTrip($args['trip_id']);

        foreach($args['users'] as $user) {
            $arr['user_id'] = $user['id'];
            $arr['destination_id'] = $stations->firstWhere('name', $user['destination'])->id;
            $arr['request_id'] = $user['request_id'];
            $data[] = $arr;
        }

        $this->businessTripSubscription->insert($data);
    }

    protected function createStationsAndDestinations(array $args, int $trip_id)
    {
        $usersData = $this->usersData($args, $trip_id);
        $destinationsData = $this->destinationsData($args['destinations'], $trip_id);
        
        $this->businessTripStation->insert(array_merge($usersData, $destinationsData));
    }

    protected function createStations(array $args)
    {
        $usersData = $this->usersData($args, $args['trip_id']);
        
        $this->businessTripStation->insert($usersData);
    }

    protected function createDestinations(array $args)
    {
        $destinationsData = $this->destinationsData($args['destinations'], $args['trip_id']);
        
        $this->businessTripStation->insert($destinationsData);
    }
    

    protected function createScheduleForEachUser(array $users, int $trip_id)
    {
        $tripScheduleArr = [
            'trip_id' => $trip_id
        ];

        foreach($users as $user) {
            $tripScheduleArr['user_id'] = $user['id'];
            $tripScheduleArr['days'] = json_encode($user['days']);
            $tripScheduleData[] = $tripScheduleArr;
        }

        $this->businessTripSchedule->upsert($tripScheduleData, ['days']);
    }

    protected function createBusinessTrip(array $input)
    {
        $businessTrip = $this->businessTrip->create($input);
        $businessTrip->update(['subscription_code' => Hashids::encode($businessTrip->id)]);

        return $businessTrip;
    }

    protected function usersData(array $args, int $trip_id)
    {
        $pickable = [
            'state' => 'PICKABLE',
            'request_type' => $args['request_type'],
            'trip_id' => $trip_id,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'accepted_at' => date('Y-m-d H:i:s'),
        ];
        foreach($args['users'] as $user) {
            $pickable['request_id'] = $user['request_id'];
            $pickable['name'] = $user['address'];
            $pickable['name_ar'] = $user['address'];
            $pickable['latitude'] = $user['lat'];
            $pickable['longitude'] = $user['lng'];
            $data[] = $pickable;
        }

        return $data;
    }

    protected function destinationsData(array $destinations, int $trip_id)
    {
        $arr = [
            'request_type' => null,
            'request_id' => null,
            'state' => 'DESTINATION',
            'trip_id' => $trip_id,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'accepted_at' => date('Y-m-d H:i:s'),
        ];
        foreach($destinations as $destination) {
            $arr['name'] = $destination['name'];
            $arr['name_ar'] = $destination['name'];
            $arr['latitude'] = $destination['lat'];
            $arr['longitude'] = $destination['lng'];
            $data[] = $arr;
        } 

        return $data;
    }

    protected function stationsData(array $args)
    {
        $arr = [
            'request_type' => $args['request_type'],
            'trip_id' => $args['trip_id'],
            'subscription_verified_at' => date('Y-m-d H:i:s'),
            'due_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'), 
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (array_key_exists('station_id', $args)) {
            $arr['station_id'] = $args['station_id'];
        }

        if (array_key_exists('destination_id', $args)) {
            $arr['destination_id'] = $args['destination_id'];
        }
        
        foreach($args['users'] as $user) {
            $arr['user_id'] = $user['id'];
            $arr['request_id'] = $user['request_id'];
            $data[] = $arr;
        } 

        return $data;
    }

    protected function subscriptionData(array $args, int $trip_id)
    {
        return [
            'request_type' => $args['request_type'],
            'trip_id' => $trip_id,
            'subscription_verified_at' => date('Y-m-d H:i:s'),
            'due_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'), 
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    protected function stationsByTrip(int $trip_id)
    {
        return $this->businessTripStation->select('id', 'request_id', 'name')
            ->where('trip_id', $trip_id)
            ->get();
    }

    protected function updateTripSchedule(array $args)
    {
        $schedule = $this->businessTrip->select('days')
            ->findOrFail($args['trip_id']);

        $this->businessTrip->where('id', $args['trip_id'])
            ->update(['days' => array_merge($schedule->days, $args['days'])]);
    }
}
