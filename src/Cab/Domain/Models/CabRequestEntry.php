<?php

namespace Aeva\Cab\Domain\Models;

use App\Helpers\ResizableMapUrl;

use Illuminate\Database\Eloquent\Model;

class CabRequestEntry extends Model
{
    protected $guarded = [];

    public $timestamps = false;

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
}
