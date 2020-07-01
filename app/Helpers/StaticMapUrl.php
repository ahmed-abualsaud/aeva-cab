<?php

namespace App\Helpers;

class StaticMapUrl
{
  public static function generate($value)
  {
    $url = "https://maps.googleapis.com/maps/api/staticmap?".
      "&scale=2".
      "&size=350x150".
      "&markers=color:0x9476E0%7C".$value->s_latitude.",".$value->s_longitude.
      "&markers=color:0x9476E0%7C".$value->d_latitude.",".$value->d_longitude.
      "&path=color:0x28077A|weight:3|enc:".$value->route_key.
      "&key=".env('GOOGLE_MAP_KEY');

    return urldecode($url);
  }
}