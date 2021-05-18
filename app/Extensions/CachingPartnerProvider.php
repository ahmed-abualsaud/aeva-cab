<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CachingPartnerProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $partner = app('cache')->get('partner.'.$identifier);
        
        if (!$partner) {
            $partner = parent::retrieveById($identifier);
            if ($partner) 
                app('cache')->put('partner.'.$identifier, $partner);
        }

        return $partner;
    }
}