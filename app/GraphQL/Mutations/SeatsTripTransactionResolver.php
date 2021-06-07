<?php

namespace App\GraphQL\Mutations;

use App\SeatsTripTransaction;

class SeatsTripTransactionResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return SeatsTripTransaction::create($input);
    }

    public function destroy($_, array $args)
    {
        return SeatsTripTransaction::whereIn('id', $args['id'])->delete();
    }
}
