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

    public static function buildMapURL($request_id)
    {
        $path = self::select('path')->where('request_id', $request_id)->first();

        if ($path) {
            self::where('request_id', $request_id)->delete();
            return ResizableMapUrl::generatePath($path->path);
        }
        return null;
    }
}
