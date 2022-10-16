<?php

namespace Aeva\Cab\Domain\Repository\Eloquent\Queries;

use App\Driver;
use App\Traits\Filterable;

use Illuminate\Support\Arr;

use Aeva\Cab\Domain\Models\CabRequest;
use Aeva\Cab\Domain\Repository\Eloquent\BaseRepository;

class CabRequestRepository extends BaseRepository
{
    use Filterable;

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

        if (array_key_exists('status', $args) && $args['status']) {
            $ret = $ret->where('Status', $args['status']);
        }

        $ret =  $ret->latest()
                    ->skip($first * $page)
                    ->take($first)
                    ->get()
                    ->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });

        [$dates, $requests] = Arr::divide($ret->toArray());

        foreach ($requests as $key => $reqs) {
            foreach ($reqs as $reqKey => $req) {
                $reqs[$reqKey]['history'] = json_encode($req['history']);
            }

            $requests[$key] = CabRequest::hydrate($reqs);
        }

        return $requests;
    }

    public function missedRequests(array $args)
    {
        $query = $this->model;

        if (array_key_exists('user_id', $args) && $args['user_id']) {
            $query = $this->model->where('user_id', $args['user_id']);
        }

        $missed_requests =  $query->where('history->missing->status', true)->get();
        foreach ($missed_requests as $req) {
            $drivers_ids = [];
            foreach ($req->history['missing']['missed'] as $missed) {
                $drivers_ids = array_values(array_unique(array_merge($drivers_ids, $missed['by'])));
            }
            $req->missed_drivers = Driver::whereIn('id', $drivers_ids)->get();
        }
        return $missed_requests;
    }

    public function stats(array $args)
    {
        if (array_key_exists('x_as_driver_name', $args) && $args['x_as_driver_name']) {
            $select_x = 'drivers.full_name as x';
        } else {
            $select_x = 'DATE_FORMAT(cab_requests.created_at, "%Y-%m-%d %H:00:00") as x';
        }

        $transactionGroup = $this->model->selectRaw($select_x.', COUNT(*) as y');

        if (array_key_exists('x_as_driver_name', $args) && $args['x_as_driver_name']) {
            $transactionGroup = $transactionGroup->join('drivers', 'cab_requests.driver_id', '=', 'drivers.id');
        }

        if (array_key_exists('period', $args) && $args['period']) {
            $transactionGroup = $this->dateFilter($args['period'], $transactionGroup, 'cab_requests.created_at');
        }
        $transactionGroup = $transactionGroup->where('cab_requests.status', 'Completed')->groupBy('x');
        return $select_x == 'drivers.full_name as x' ? $transactionGroup->take(30)->cursor() : $transactionGroup->cursor();
    }
}
