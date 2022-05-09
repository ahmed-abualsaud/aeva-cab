<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Queries;

use Illuminate\Support\Arr;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;

class CabRequestRepository extends BaseRepository
{
    public function __construct(CabRequest $model)
    {
        parent::__construct($model);
    }

    public function history(array $args) 
    {
        $first = 10;
        $page = 0;

        if (array_key_exists('first', $args) && $args['first']) {
            $first = $args['first'];
        }

        if (array_key_exists('page', $args) && $args['page'] > 0) {
            $page = $args['page'] - 1;
        }

        if (array_key_exists('user_id', $args) && $args['user_id']) {
            $ret = $this->model->where('user_id', $args['user_id']);
        }

        if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            $ret = $this->model->where('driver_id', $args['driver_id']);
        }

        $ret =  $ret->latest()
                    ->skip($first * $page)
                    ->take($first)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });
        
        [$dates, $requests] = Arr::divide($ret->toArray());

        foreach ($requests as $key => $request) {
            $requests[$key] = CabRequest::hydrate($request);
        }

        return $requests;
    }
}