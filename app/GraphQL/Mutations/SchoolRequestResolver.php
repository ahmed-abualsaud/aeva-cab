<?php

namespace App\GraphQL\Mutations;

use App\SchoolRequest;

class SchoolRequestResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function destroy($_, array $args)
    {
        return SchoolRequest::whereIn('id', $args['id'])->delete();
    }
}
