<?php

namespace App\Repository\Eloquent\Queries;   

use App\Repository\Queries\OndemandRequestRepositoryInterface;
use App\OndemandRequest;
use App\Traits\Filterable;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class OndemandRequestRepository extends BaseRepository implements OndemandRequestRepositoryInterface
{
    use Filterable;

    public function __construct(OndemandRequest $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function invoke(array $args)
    {
        try {
            $req = $this->model->findOrFail($args['id']);

            if (array_key_exists('nav', $args) && $args['nav']) {
                if (!$req->read_at) $req->update(["read_at" => date('Y-m-d H:i:s')]);

                $next = $this->model->select('id')
                    ->where('id', '<', $req->id)
                    ->orderBy('id','desc')
                    ->first();
                $previous = $this->model->select('id')
                    ->where('id', '>', $req->id)
                    ->orderBy('id','asc')
                    ->first();

                $req->next = $next ? $next->id : null;
                $req->previous = $previous ? $previous->id : null;
            }
        } catch (ModelNotFoundException $e) {
            throw new CustomException($e->getMessage(), "modelNotFound");
        }
        
        return $req;
    }

    public function stats(array $args)
    {
        $requestCount = $this->model->query();

        $requestGroup = $this->model->selectRaw('
            DATE_FORMAT(created_at, "%a, %b %d, %Y") as date,
            count(*) as count
        ');

        if (array_key_exists('period', $args) && $args['period']) {
            $requestCount = $this->dateFilter($args['period'], $requestCount, 'created_at');
            $requestGroup = $this->dateFilter($args['period'], $requestGroup, 'created_at');
        }

        if (array_key_exists('status', $args) && $args['status']) {
            $requestCount = $requestCount->where('status', $args['status']);
            $requestGroup = $requestGroup->where('status', $args['status']);
        }

        $requestCount = $requestCount->count();
        $requestGroup = $requestGroup->groupBy('date')->get();

        $response = [
            "count" => $requestCount,
            "requests" => $requestGroup
        ];

        return $response;
    }
}