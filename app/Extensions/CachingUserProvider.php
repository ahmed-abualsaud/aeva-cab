<?php

namespace App\Extensions;

use Illuminate\Auth\EloquentUserProvider;

class CachingUserProvider extends EloquentUserProvider
{
  public function retrieveById($identifier)
  {
    $user = app('cache')->get('user.'.$identifier);
    if (!$user) {
      $user = parent::retrieveById($identifier);
      if ($user) 
        app('cache')->put('user.'.$identifier, $user);
    }

    return $user;
  }
}