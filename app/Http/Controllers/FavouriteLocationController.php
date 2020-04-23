<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Auth;
use DB;
use App\FavouriteLocation;
use App\UserRequest;

class FavouriteLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $HomeLocation = FavouriteLocation::where(['type'=>'HOME','user_id'=>Auth::guard('user')->user()->id])->get();
        $WorkLocation = FavouriteLocation::where(['type'=>'WORK','user_id'=>Auth::guard('user')->user()->id])->get();
        $OthersLocation = FavouriteLocation::where(['type'=>'OTHER','user_id'=>Auth::guard('user')->user()->id])->get();

        $SourceAddressRecent = UserRequest::select(['id','user_id','s_address as address','s_latitude as latitude','s_longitude as longitude',DB::Raw('"RECENT" as type')])->where('user_id',Auth::guard('user')->user()->id)->distinct('s_address')->orderBy('id','desc');
        $DistinationAddressRecent = UserRequest::select(['id','user_id','d_address as address','d_latitude as latitude','d_longitude as longitude',DB::Raw('"RECENT" as type')])->where('user_id',Auth::guard('user')->user()->id)->distinct('d_address')->orderBy('id','desc');

        $RecentLocation = $SourceAddressRecent->union($DistinationAddressRecent)->orderBy('id','desc')->skip(0)->take(10)->get();
        $SearchLocation = ["HOME" => $HomeLocation,"WORK"=>$WorkLocation,"OTHER"=>$OthersLocation,
            "RECENT"=>$RecentLocation];
 
        return $SearchLocation;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]);

        try {
            $Location['user_id'] = Auth::guard('user')->user()->id;
            $Location['address'] = $request->address;
            $Location['latitude'] = $request->latitude;
            $Location['longitude'] = $request->longitude;
            $Location['type'] = $request->type;

            $IsExists = FavouriteLocation::where($Location)->count();

            if ($IsExists == 0) {
                FavouriteLocation::create($Location);
                return response()->json(['message' => 'Favourite Location Saved Successfully'],200); 
            } else {
                return response()->json(['error' => 'Favourite Location Already Exists'],400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Favourite Location Not Found'],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $Favourite = FavouriteLocation::findOrFail($id);
            return $Favourite;  
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Favourite Location Not Found'],500); 
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $Favourite = FavouriteLocation::findOrFail($id);
            return $Favourite; 
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Favourite Location Not Found'],500); 
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]);

        try{
            $UpdateLocation = FavouriteLocation::findOrFail($id);

            $Location['user_id']   = Auth::guard('user')->user()->id;
            $Location['address']   = $request->address;
            $Location['latitude']  = $request->latitude;
            $Location['longitude'] = $request->longitude;
            $Location['type']      = $request->type;
            $IsExists = FavouriteLocation::where($Location)->count();

            if ($IsExists == 0) {
                $UpdateLocation->user_id = Auth::guard('user')->user()->id;
                $UpdateLocation->address = $request->address;
                $UpdateLocation->latitude = $request->latitude;
                $UpdateLocation->longitude = $request->longitude;
                $UpdateLocation->type = $request->type;
                $UpdateLocation->save();
                return response()->json(['message' => 'Favourite Location Updated Successfully'], 200);
            } else {
                return response()->json(['error' => 'Favourite Location Already Exists'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Favourite Location Not Found'],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            FavouriteLocation::find($id)->delete();
            return response()->json(['message' => 'Favourite location deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Favourite location Not Found'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Favourite location Not Found'], 404);
        }
    }
}
