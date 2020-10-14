<?php

namespace App\GraphQL\Queries;

use App\OndemandRequest;
use App\Traits\Filterable;

class OndemandRequestResolver
{
    use Filterable;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $req = OndemandRequest::findOrFail($args['id']);

        if (array_key_exists('nav', $args) && $args['nav']) {
            if (!$req->read_at) $req->update(["read_at" => now()]);

            $next = OndemandRequest::select('id')
                ->where('id', '<', $req->id)
                ->orderBy('id','desc')
                ->first();
            $previous = OndemandRequest::select('id')
                ->where('id', '>', $req->id)
                ->orderBy('id','asc')
                ->first();

            $req->next = $next ? $next->id : null;
            $req->previous = $previous ? $previous->id : null;
        }

        return $req;
    }

    public function stats($_, array $args)
    {
        $requestCount = OndemandRequest::query();

        $requestGroup = OndemandRequest::selectRaw('
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
