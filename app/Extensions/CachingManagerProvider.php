<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CachingManagerProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $manager = app('cache')->get('manager.'.$identifier);
        
        if (!$manager) {
            $manager = parent::retrieveById($identifier);
            if ($manager) 
                app('cache')->put('manager.'.$identifier, $manager);
        }

        return $manager;
    }
}