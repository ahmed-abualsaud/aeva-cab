<?php

namespace Aeva\Cab\Domain\Models;

use App\Helpers\ResizableMapUrl;

use Illuminate\Database\Eloquent\Model;

class CabRequestEntry extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    public static function calculateDistance($args)
    {
        $last_location = self::getLastLocation($args['request_id']);
        if($last_location) {
            return (self::sphereDistance(
                $args['latitude'], 
                $args['longitude'], 
                $last_location->latitude, 
                $last_location->longitude
            ) + $last_location->distance);
        }
        return 0;
    }

    public static function getLastLocation($request_id)
    {
        return  self::where('request_id', $request_id)->latest()->first();
    }

    public static function removeEntriesAndBuildMapURL($request_id)
    {
        $locations = self::select('latitude', 'longitude')
                ->where('request_id', $request_id)
                ->get();

        if ($locations->isNotEmpty()) {
            foreach($locations as $loc) {
                $path[] = $loc->latitude.','.$loc->longitude;
            }
            self::where('request_id', $request_id)->delete();
            return ResizableMapUrl::generatePath(implode('|', $path));
        }
        return null;
    }

    protected static function sphereDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) +  cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);
        return acos($dist) / $rad * 60 * 1853;
    }
}
