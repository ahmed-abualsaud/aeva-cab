<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HandleAccessTokenCache
{
    protected function handleAccessTokenCache($guard, $entity, $token)
    {
        $cachedToken = $this->getCachedToken($guard, $entity->id);

        if ($cachedToken) {
            $this->invalidateToken($guard, $cachedToken);
            $this->removeCachedToken($guard, $entity->id);
        }

        $this->cacheToken($guard, $entity->id, $token);
    }

    protected function cacheToken($guard, $id, $token){
        return Cache::put($guard.'_'.$id.'_token', $token);
    }

    protected function getCachedToken($guard, $id){
        return Cache::get($guard.'_'.$id.'_token');
    }

    protected function removeCachedToken($guard, $id){
        return Cache::forget($guard.'_'.$id.'_token');
    }

    protected function invalidateToken($guard, $token)
    {
        return auth($guard)->setToken($token)->invalidate(true);
    }
}