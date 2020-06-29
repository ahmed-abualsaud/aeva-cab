<?php

namespace App\Http\Controllers;

use App\CabRequest;
use App\FavouriteLocation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class FavouriteLocationController extends Controller
{
    public function index()
    {
        $userID = auth('user')->user()->id;
        
        $homeLocation = FavouriteLocation::where('type', 'HOME')
            ->where('user_id', $userID)
            ->get();
        $workLocation = FavouriteLocation::where('type', 'WORK')
            ->where('user_id', $userID)
            ->get();
        $othersLocation = FavouriteLocation::where('type', 'OTHER')
            ->where('user_id', $userID)
            ->get();

        $sourceAddressRecent = CabRequest::selectRaw('id, user_id, s_address as address, s_latitude as latitude, s_longitude as longitude, "RECENT" as type')
            ->where('user_id', $userID)
            ->distinct('s_address')
            ->take(5)
            ->orderBy('id', 'desc');

        $distinationAddressRecent = CabRequest::selectRaw('id, user_id, d_address as address, d_latitude as latitude, d_longitude as longitude, "RECENT" as type')
            ->where('user_id', $userID)
            ->distinct('d_address')
            ->take(5)
            ->orderBy('id', 'desc');

        $recentLocation = $sourceAddressRecent->union($distinationAddressRecent)
            ->orderBy('id', 'desc')
            ->get();
 
        return [
            "HOME" => $homeLocation,
            "WORK"=> $workLocation,
            "OTHER"=> $othersLocation,
            "RECENT"=> $recentLocation
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $location['user_id'] = auth('user')->user()->id;
        $location['address'] = $request->address;
        $location['latitude'] = $request->latitude;
        $location['longitude'] = $request->longitude;
        $location['type'] = $request->type;

        try {
            FavouriteLocation::where($location)->firstOrFail();
        } catch(ModelNotFoundException $e) {
            FavouriteLocation::create($location);
            return response()->json(['message' => 'Location saved successfully']);
        }

        return response()->json(['error' => 'This location already exists'], 400);

    }

    public function show($id)
    {
        try {
            return FavouriteLocation::findOrFail($id); 
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Location not found'], 500); 
        }
    }

    public function update(Request $request, $id)
    {        
        $validator = Validator::make($request->all(), [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]); 

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $updateLocation = FavouriteLocation::findOrFail($id);

            $location['user_id']   = auth('user')->user()->id;
            $location['address']   = $request->address;
            $location['latitude']  = $request->latitude;
            $location['longitude'] = $request->longitude;
            $location['type']      = $request->type;

            try {
                FavouriteLocation::where($location)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $updateLocation->user_id = auth('user')->user()->id;
                $updateLocation->address = $request->address;
                $updateLocation->latitude = $request->latitude;
                $updateLocation->longitude = $request->longitude;
                $updateLocation->type = $request->type;
                $updateLocation->save();
                return response()->json(['message' => 'Location updated successfully']);
            }

            return response()->json(['error' => 'This location already exists'], 500);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Location not found'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $location = FavouriteLocation::findOrFail($id);
            $location->delete();
            return response()->json(['message' => 'Location deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Location not found'], 404);
        }
    }
}
