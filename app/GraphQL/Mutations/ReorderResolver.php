<?php

namespace App\GraphQL\Mutations;

class ReorderResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        return $args['model']::reorder($args['orders']);
    }
}
