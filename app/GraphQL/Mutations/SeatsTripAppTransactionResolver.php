<?php

namespace App\GraphQL\Mutations;

use App\SeatsTripAppTransaction;

class SeatsTripAppTransactionResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();
        return SeatsTripAppTransaction::create($input);
    }

    public function destroy($_, array $args)
    {
        return SeatsTripAppTransaction::whereIn('id', $args['id'])->delete();
    }
}
