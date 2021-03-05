<?php

namespace App\GraphQL\Queries;

use App\Document;

class DocumentResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $documents = Document::where('documentable_id', $args['documentable_id'])
            ->where('documentable_type', $args['documentable_type'])
            ->get();

        return $documents;
    }
}
