<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\SeatsTripEventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeatsTripEventController 
{
    private $seatsTripEventRepository;

    public function __construct(SeatsTripEventRepositoryInterface $seatsTripEventRepository)
    {
        $this->seatsTripEventRepository = $seatsTripEventRepository;
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
            return $validator->errors();

        return $this->seatsTripEventRepository->ready($request->all());
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
            return $validator->errors();

        return $this->seatsTripEventRepository->start($request->all());
    }

    public function updateDriverLocation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'log_id' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required']
        ]);

        if ($validator->fails())
            return $validator->errors();

        return $this->seatsTripEventRepository->updateDriverLocation($request->all());
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
            return $validator->errors();

        return $this->seatsTripEventRepository->atStation($request->all());
    }

    public function pickUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'booking_id' => ['required']
        ]);

        if ($validator->fails())
            return $validator->errors();

        return $this->seatsTripEventRepository->pickUser($request->all());
    }

    public function dropUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'log_id' => ['required'],
            'booking_id' => ['required'],
            'user_id' => ['required'],
            'trip_id' => ['required'],
            'trip_time' => ['required'],
            'driver_id' => ['required'],
            'payable' => ['required'],
            'paid' => ['required']
        ]);

        if ($validator->fails())
            return $validator->errors();

        return $this->seatsTripEventRepository->dropUser($request->all());
    }

    public function end(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'trip_id' => ['required']
        ]);

        if ($validator->fails())
            return $validator->errors();
            
        return $this->seatsTripEventRepository->end($request->all());
    }
}