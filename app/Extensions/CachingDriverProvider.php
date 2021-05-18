<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CachingDriverProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $driver = app('cache')->get('driver.'.$identifier);
        
        if (!$driver) {
            $driver = parent::retrieveById($identifier);
            if ($driver) 
                app('cache')->put('driver.'.$identifier, $driver);
        }

        return $driver;
    }
}