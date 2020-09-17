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
        if (!$req->read_at) $req->update(["read_at" => now()]);

        return $req;
    }
}
