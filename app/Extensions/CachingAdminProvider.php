<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CachingAdminProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $admin = app('cache')->get('admin.'.$identifier);
        
        if (!$admin) {
            $model = $this->createModel();
            $admin = $model->newQuery()
                // ->with('role')
                ->where($model->getAuthIdentifierName(), $identifier)
                ->first();

            if ($admin) 
                app('cache')->put('admin.'.$identifier, $admin);
        }

        return $admin;
    }
}