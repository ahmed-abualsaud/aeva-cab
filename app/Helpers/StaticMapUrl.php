<?php

namespace App\Helpers;

class StaticMapUrl
{
  public static function generate($value)
  {
    return "https://maps.googleapis.com/maps/api/staticmap?".
      "&zoom=12".
      "&size=320x130".
      "&maptype=roadmap".
      "&format=png".
      "&markers=size:small%7Ccolor:0x321284%7C".$value->s_latitude.",".$value->s_longitude.
      "&markers=size:small%7Ccolor:0x1a7386%7C".$value->d_latitude.",".$value->d_longitude.
      "&path=color:0x1a7386|weight:3|enc:".$value->route_key.
      "&key=".env('GOOGLE_MAP_KEY');
  }
}