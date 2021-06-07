<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripSubscription;
use Illuminate\Support\Carbon;
use App\BusinessTripTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BusinessTripTransactionResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        DB::beginTransaction();
        try {
            $input = collect($args)->except(['directive'])->toArray();

            BusinessTripSubscription::where('trip_id', $args['trip_id'])
                ->where('user_id', $args['user_id'])
                ->update(['due_date' => Carbon::parse($args['due_date'])->addMonth()]);
    
            BusinessTripTransaction::create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException('Could not create transaction');
        }

        return true;

    }

    public function destroy($_, array $args)
    {
        return BusinessTripTransaction::whereIn('id', $args['id'])->delete();
    }
}
