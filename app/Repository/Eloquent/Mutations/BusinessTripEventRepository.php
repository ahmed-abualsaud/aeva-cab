<?php

namespace App\Repository\Eloquent\Mutations;

use App\Driver;
use App\Student;
use App\BusinessTrip;
use App\BusinessTripEntry;
use App\BusinessTripEvent;
use App\BusinessTripRating;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\StudentSubscription;
use App\BusinessTripSchedule;
use App\Helpers\StaticMapUrl;
use App\BusinessTripAttendance;
use App\Jobs\SendPushNotification;
use App\Traits\HandleDeviceTokens;
use App\Exceptions\CustomException;
use App\Events\BusinessTripStatusChanged;
use App\Traits\HandleBusinessTripUserStatus;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\Mutations\BusinessTripEventRepositoryInterface;

class BusinessTripEventRepository extends BaseRepository implements BusinessTripEventRepositoryInterface
{
    use HandleDeviceTokens;
    use HandleBusinessTripUserStatus;
    
    public function __construct(BusinessTripEvent $model)
    {
        parent::__construct($model);
    }

    public function ready(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if ($trip->log_id) 
            throw new CustomException(__('lang.trip_already_started'));

        $logId = (string) Str::uuid();

        $this->checkAbsence($args['trip_id']);

        $this->checkSchedule($args['trip_id']);

        $this->initTripEvent($args, $logId, $trip->driver_id, $trip->vehicle_id);

        $trip->update(['log_id' => $logId, 'ready_at' => date("Y-m-d H:i:s")]);

        return $trip;
    }

    public function start(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->log_id) 
            throw new CustomException(__('lang.driver_not_ready'));

        $payload = [
            'started' => [
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'],
                'lng' => $args['longitude']
            ]
        ];

        $event = $this->model->select('content', 'log_id')->findOrFail($trip->log_id);

        $event->update(['content' => array_merge($event->content, $payload)]);

        SendPushNotification::dispatch(
            $this->tripUsersToken($trip->id),
            __('lang.trip_started'),
            $trip->name,
            ['view' => 'BusinessTrip', 'id' => $args['trip_id']]
        );

        Driver::updateLocation($args['latitude'], $args['longitude']);

        $this->broadcastTripStatus($trip, ['status' => 'STARTED', 'log_id' => $trip->log_id]);

        $trip->update(['starts_at' => $args['trip_time']]);

        return $trip;
    }

    public function atStation(array $args)
    {
        try { 
            SendPushNotification::dispatch(
                $this->stationUsersToken($args['station_id'], $args['trip_id']), 
                __('lang.captain_arrived'),
                $args['trip_name'],
                ['view' => 'BusinessTrip', 'id' => $args['trip_id']]
            );

            $payload = array([
                'station_id' => $args['station_id'],
                'station_name' => $args['station_name'],
                'status' => 'at station',
                'at' => date("Y-m-d H:i:s"),
                'eta' => $args['eta'],
                'lat' => $args['latitude'],
                'lng' => $args['longitude']
            ]);
            
            return $this->updateEventPayload($args['log_id'], $payload);

        } catch (\Exception $e) {
            throw new CustomException(__('lang.notify_station_failed'));
        }
    }

    public function changePickupStatus(array $args)
    {
        $data = [
            'status' => $args['is_picked_up'] ? 'picked up' : 'dropped off',
            'at' => date("Y-m-d H:i:s"),
            'lat' => $args['latitude'],
            'lng' => $args['longitude'],
            'by' => 'user'
        ];

        if(BusinessTrip::find($args['trip_id'])['type'] == 'TOSCHOOL') {
            $this->updateStudentStatus(
                $args['trip_id'], ['is_picked_up' => $args['is_picked_up']], $args['user_id']
            );

            $data['student_id'] = $args['user_id'];
            $data['student_name'] = $args['user_name'];
        }
        else {
            $this->updateUserStatus(
                $args['trip_id'], ['is_picked_up' => $args['is_picked_up']], $args['user_id']
            );

            $data['user_id'] = $args['user_id'];
            $data['user_name'] = $args['user_name'];
        }

        return $this->updateEventPayload($args['log_id'], array($data));
    }

    public function changeAttendanceStatus(array $args)
    {
        BusinessTripAttendance::updateOrCreate(
            ['date' => $args['date'], 'trip_id' => $args['trip_id'], 'user_id' => $args['user_id']], 
            ['is_absent' => $args['is_absent']]
        );

        $data = [
            'status' => $args['is_absent'] ? 'absent' : 'present',
            'at' => date("Y-m-d H:i:s"),
            'lat' => $args['latitude'],
            'lng' => $args['longitude'],
            'by' => $args['by']
        ];

        if(BusinessTrip::find($args['trip_id'])['type'] == 'TOSCHOOL') 
        {
            $students_ids = BusinessTripAttendance::whereStudents($args);
            $students = Student::select('id', 'name')->whereIn('id', $students_ids)->get();
            
            $this->updateStudentStatus(
                $args['trip_id'], ['is_absent' => $args['is_absent']], $students_ids
            );

            foreach($students as $student) {
                $data['student_id'] = $student['id'];
                $data['student_name'] = $student['name'];
                $payload[] = $data;
            }
        }
        else 
        {
            $this->updateUserStatus(
                $args['trip_id'], ['is_absent' => $args['is_absent']], $args['user_id']
            );

            $data['user_id'] = $args['user_id'];
            $data['user_name'] = $args['user_name'];
            $payload[] = $data;
        }

        $this->attendanceNotification($args);

        return $this->updateEventPayload($args['log_id'], $payload);
    }

    public function pickUsers(array $args)
    {
        $msg = __('lang.welcome_trip');

        if(BusinessTrip::find($args['trip_id'])['type'] == 'TOSCHOOL') {
            return $this->pickOrDropStudents($args, true, $msg);
        }
        else {
            return $this->pickOrDropUsers($args, true, $msg);
        }
    }

    public function dropUsers(array $args)
    {
        $msg = __('lang.bye_trip');

        if(BusinessTrip::find($args['trip_id'])['type'] == 'TOSCHOOL') {
            return $this->pickOrDropStudents($args, false, $msg);
        }
        else
        {
            $this->createUsersRatings($args);
            return $this->pickOrDropUsers($args, false, $msg);
        }
    }

    public function updateDriverLocation(array $args)
    {
        try {
            $input = [
                'log_id' => $args['log_id'],
                'latitude' => $args['latitude'],
                'longitude' => $args['longitude']
            ];
            Driver::updateLocation($args['latitude'], $args['longitude']);
            return BusinessTripEntry::create($input);
        } catch (\Exception $e) {
            //
        }
    }

    public function end(array $args)
    {
        $trip = $this->getTripById($args['trip_id']);

        if (!$trip->log_id) 
            throw new CustomException(__('lang.trip_ended'));

        $logId = $trip->log_id;

        $trip->update(['log_id' => null, 'starts_at' => null, 'ready_at' => null]);

        if(BusinessTrip::find($args['trip_id'])['type'] == 'TOSCHOOL')
        {
            $this->updateStudentStatus(
                $args['trip_id'],
                ['is_picked_up' => false, 'is_absent' => false, 'is_scheduled' => true]
            );
        }
        else {
            $this->updateUserStatus(
                $args['trip_id'],
                ['is_picked_up' => false, 'is_absent' => false, 'is_scheduled' => true]
            );
        }

        return $this->closeTripEvent($args, $logId, $trip);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('log_id', $args['log_id'])->delete();
    }

    protected function getTripById($id)
    {
        try {
            return BusinessTrip::select(
                'business_trips.id', 'business_trips.name', 
                'business_trips.log_id', 'business_trips.type',
                'drivers.id as driver_id', 'drivers.name as driver_name',
                'drivers.latitude as driver_lat', 'drivers.longitude as driver_lng',
                'partners.id as partner_id', 'partners.name as partner_name',
                'vehicle_id'
            )
            ->join('drivers', 'drivers.id', '=', 'business_trips.driver_id')
            ->join('partners', 'partners.id', '=', 'business_trips.partner_id')
            ->findOrFail($id);
        } catch (\Exception $e) {
            throw new CustomException('Could not find this trip!');
        }
    }

    protected function pickOrDropUsers($args, $is_picked_up, $msg)
    {
        try {
            $user_ids = Arr::pluck($args['users'], 'id');

            $this->updateUserStatus(
                $args['trip_id'], ['is_picked_up' => $is_picked_up], $user_ids
            );

            SendPushNotification::dispatch(
                $this->usersToken($args['trip_id'], $user_ids), 
                $msg, 
                $args['trip_name'],
                ['view' => 'BusinessTripUserStatus', 'id' => $args['trip_id']]
            );

            $payload = [
                'status' => $is_picked_up ? 'picked up' : 'dropped off',
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'], 
                'lng' => $args['longitude'],
                'by' => 'driver'
            ];

            foreach($args['users'] as $user) {
                $payload['user_id'] = $user['id'];
                $payload['user_name'] = $user['name'];
                $data[] = $payload;
            }

            return $this->updateEventPayload($args['log_id'], $data);

        } catch (\Exception $e) {
            throw new CustomException(__('lang.change_user_status_failed'));
        }
    }

    protected function pickOrDropStudents($args, $is_picked_up, $msg)
    {
        try {
            $student_ids = Arr::pluck($args['users'], 'id');

            $this->updateStudentStatus(
                $args['trip_id'], ['is_picked_up' => $is_picked_up], $student_ids
            );

            SendPushNotification::dispatch(
                $this->usersToken($this->getStudentsParents($args['trip_id'], $student_ids)), 
                $msg, 
                $args['trip_name'],
                ['view' => 'BusinessTripUserStatus', 'id' => $args['trip_id']]
            );

            $payload = [
                'status' => $is_picked_up ? 'picked up' : 'dropped off',
                'at' => date("Y-m-d H:i:s"),
                'lat' => $args['latitude'], 
                'lng' => $args['longitude'],
                'by' => 'driver'
            ];

            foreach($args['users'] as $student) {
                $payload['student_id'] = $student['id'];
                $payload['student_name'] = $student['name'];
                $data[] = $payload;
            }

            return $this->updateEventPayload($args['log_id'], $data);

        } catch (\Exception $e) {
            throw new CustomException(__('lang.change_user_status_failed'));
        }
    }

    protected function updateEventPayload($logId, $payload)
    {
        try {
            $event = $this->model->select('content', 'log_id')
                ->findOrFail($logId);
    
            if (array_key_exists('payload', $event->content)) 
                $payload = array_merge($event->content['payload'], $payload);
                
            return $event->update(['content' => array_merge($event->content, ['payload' => $payload])]);
        } catch (\Exception $e) {
            //
        }
    }

    protected function closeTripEvent($args, $logId, $trip)
    {
        try {
            $event = $this->model->select('content', 'log_id')
                ->findOrFail($logId);

            $locations = BusinessTripEntry::select('latitude', 'longitude')
                ->where('log_id', $logId)
                ->get();

            if ($locations->isNotEmpty()) {
                foreach($locations as $loc) 
                    $path[] = $loc->latitude.','.$loc->longitude;

                $updatedData['map_url'] = StaticMapUrl::generatePath(implode('|', $path));

                BusinessTripEntry::where('log_id', $logId)
                    ->delete();
            }

            $ended = ['at' => date("Y-m-d H:i:s")];

            if (array_key_exists('latitude', $args) && array_key_exists('longitude', $args)) {
                $ended['lat'] = $args['latitude'];
                $ended['lng'] = $args['longitude'];

                $this->broadcastTripStatus($trip, ['status' => 'ENDED', 'log_id' => null]);
            }

            $updatedData['content'] = array_merge($event->content, ['ended' => $ended]);

            return $event->update($updatedData);
        } catch (\Exception $e) {
            //
        }
    }

    protected function attendanceNotification($args)
    {
        try {
            $status_text = $args['is_absent'] ? 'Absent' : 'Present';

            switch($args['by']) {
                case 'user':
                    $token = $this->driverToken($args['driver_id']);

                    $msg = __('lang.attendence_changed', [
                            'user' => $args['user_name'],
                            'status' => $status_text,
                        ]);
                    break;
                default:
                    $token = $this->userToken($args['trip_id'], $args['user_id']);
                    $msg = __('lang.captain_changed_attendance', 
                        ['status' => $status_text]);
            }

            SendPushNotification::dispatch(
                $token, 
                $msg, 
                $args['trip_name'],
                ['view' => 'BusinessTripUserStatus', 'id' => $args['trip_id']]
            );

        } catch(\Exception $e) {
            //
        }
    }

    protected function checkAbsence($trip_id)
    {
        try {
            if(BusinessTrip::find($trip_id)['type'] == 'TOSCHOOL')
            {
                $absent_students = BusinessTripAttendance::whereAbsentStudents($trip_id);

                if ($absent_students) 
                    $this->updateStudentStatus($trip_id, ['is_absent' => true], $absent_students);
            }
            else
            {
                $absent_users = BusinessTripAttendance::whereAbsent($trip_id);

                if ($absent_users) 
                    $this->updateUserStatus($trip_id, ['is_absent' => true], $absent_users);
            }
        } catch(\Exception $e) {
            //
        }
    }

    protected function checkSchedule($trip_id)
    {
        try {
            if(BusinessTrip::find($trip_id)['type'] == 'TOSCHOOL') 
            {
                StudentSubscription::whereNotScheduled($trip_id)->update(['is_scheduled' => false]);
            }
            else {
                $not_scheduled_users = BusinessTripSchedule::whereNotScheduled($trip_id);

                if ($not_scheduled_users) 
                    $this->updateUserStatus($trip_id, ['is_scheduled' => false], $not_scheduled_users);
            }
        } catch(\Exception $e) {
            //
        }
    }

    protected function initTripEvent($args, $logId, $driverId, $vehicleId)
    {
        try {
            $input = [
                'trip_id' => $args['trip_id'],
                'trip_time' => $args['trip_time'],
                'driver_id' => $driverId,
                'vehicle_id' => $vehicleId,
                'log_id' => $logId,
                'content' => [ 
                    'ready' => [
                        'at' => date("Y-m-d H:i:s"),
                        'lat' => $args['latitude'],
                        'lng' => $args['longitude']
                    ]
                ]
            ];
            $this->model->create($input);
        } catch (\Exception $e) {
            //
        }
    }

    protected function broadcastTripStatus($trip, $input)
    {
        $data = [
            'id' => $trip->id,
            'log_id' => $input['log_id'],
            'name' => $trip->name,
            'status' => $input['status'],
            'type' => $trip->type,
            'partner' => [
                'id' => $trip->partner_id,
                'name' => $trip->partner_name,
                '__typename' => 'Partner'
            ],
            'driver' => [
                'id' => $trip->driver_id,
                'name' => $trip->driver_name,
                'latitude' => $trip->driver_lat,
                'longitude' => $trip->driver_lng,
                '__typename' => 'Driver'
            ],
            '__typename' => 'BusinessTrip'
        ];
        broadcast(new BusinessTripStatusChanged($data));
    }

    protected function createUsersRatings($args)
    {
        $user_ids = Arr::pluck($args['users'], 'id');

        $arr = [
            'trip_id' => $args['trip_id'],
            'log_id' => $args['log_id'],
            'trip_time' => $args['trip_time'],
            'driver_id' => $args['driver_id'],
            'created_at' => now(), 
            'updated_at' => now()
        ];

        foreach($user_ids as $user_id) {
            $arr['user_id'] = $user_id;
            $data[] = $arr;
        }

        BusinessTripRating::insert($data);
    }
}

