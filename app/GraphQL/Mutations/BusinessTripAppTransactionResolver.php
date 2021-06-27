<?php

namespace App\GraphQL\Mutations;

use App\BusinessTripSubscription;
use Illuminate\Support\Carbon;
use App\BusinessTripAppTransaction;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class BusinessTripAppTransactionResolver
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
    
            BusinessTripAppTransaction::create($input);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new CustomException(__('lang.create_trnx_failed'));
        }

        return true;

    }

    public function destroy($_, array $args)
    {
        return BusinessTripAppTransaction::whereIn('id', $args['id'])->delete();
    }
}
