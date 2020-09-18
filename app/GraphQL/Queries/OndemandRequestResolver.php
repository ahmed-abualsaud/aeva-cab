<?php

namespace App\GraphQL\Queries;

use App\OndemandRequest;

class OndemandRequestResolver
{
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
}
