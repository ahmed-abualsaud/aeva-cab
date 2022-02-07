<?php

namespace App\Helpers;

class StaticMapUrl
{
  private static $map = "https://maps.googleapis.com/maps/api/staticmap?scale=2&style=element:geometry%7Cvisibility:on%7Ccolor:0xe0e4e7&style=element:labels.text.fill%7Cvisibility:on%7Ccolor:0x5e6580&style=feature:road%7Celement:geometry%7Cvisibility:simplified%7Ccolor:0xffffff&style=feature:road%7Celement:geometry.stroke%7Cvisibility:simplified%7Ccolor:0xcfd4d9&style=feature:road.arterial%7Celement:geometry%7Cvisibility:simplified%7Ccolor:0xffffff&style=feature:road.highway%7Celement:geometry%7Cvisibility:simplified%7Ccolor:0xa3aacc&style=feature:road.highway%7Celement:geometry.stroke%7Cvisibility:simplified%7Ccolor:0xffffff&style=feature:poi.park%7Celement:geometry.fill%7Ccolor:0x98daa7&style=feature:poi.business%7Cvisibility:off&style=feature:transit.line%7Cvisibility:simplified%7Ccolor:0xa3aacc&style=feature:transit.station%7Celement:geometry%7Cvisibility:simplified%7Ccolor:0xcfd4d9&style=feature:water%7Celement:geometry%7Cvisibility:simplified%7Ccolor:0xdce2e1";

  public static function generatePolylines($value)
  {
    $url = self::$map."&size=640x500&markers=color:0x9476E0%7C".$value->s_lat.",".$value->s_lng."&markers=color:0x9476E0%7C".$value->d_lat.",".$value->d_lng."&path=color:0x28077A|weight:3|enc:".$value->route_key."&key=".config('custom.google_map_key');

    return urldecode($url);
  }

  public static function generatePath($path)
  {
    $url = self::$map."&size=640x450&path=color:blue|weight:4|".$path."&key=".config('custom.google_map_key');
    
    return urldecode($url);
  }
}