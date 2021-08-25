<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\BusinessTripEventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Traits\HandleValidatorMessages;

class BusinessTripEventController 
{
    use HandleValidatorMessages;

    private $businessTripEventRepository;

    public function __construct(BusinessTripEventRepositoryInterface $businessTripEventRepository)
    {
        $this->businessTripEventRepository = $businessTripEventRepository;
    }

    public function ready(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->ready($request->all());
    }

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->start($request->all());
    }

    public function atStation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'eta' => ['required'],
            'station_id' => ['required'],
            'station_name' => ['required'],
            'log_id' => ['required'],
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->atStation($request->all());
    }

    public function changeAttendanceStatus(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date' => ['required'],
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'user_id' => ['required'],
            'user_name' => ['required'],
            'is_absent' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'driver_id' => ['required'],
            'by' => ['required', Rule::in(['driver', 'user'])]
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->changeAttendanceStatus($request->all());
    }

    public function pickUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'users' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->pickUsers($request->all());
    }

    public function dropUsers(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required'],
            'trip_name' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'users' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->dropUsers($request->all());
    }

    public function updateDriverLocation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->updateDriverLocation($request->all());
    }

    public function end(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required']
        ]);

        if ($validator->fails())
            return response()->json($this->handleValidatorMessages($validator->errors()), 400);

        return $this->businessTripEventRepository->end($request->all());
    }
}