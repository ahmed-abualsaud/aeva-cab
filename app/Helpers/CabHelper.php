<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/** @noinspection PhpMissingReturnTypeInspection */

use Aeva\Cab\Domain\Models\Trace;
use App\Driver;
use App\DriverStats;
use App\Traits\BulkQuery\BulkQuery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\LazyCollection;

const BOOLEANS = ['true','false','1','0',true,false,0,1,'on','off','yes','no','True','False'];
const BOOLEAN_FALSE = ['false',false,0,'off'];
const BOOLEAN_TRUE = ['true',true,0,'no'];

/**
 * @param string|null $amount
 * @return bool
 */
function is_zero(?string $amount) : bool
{
    try {
        return (empty($amount) or custom_number($amount) == zero());
    }catch (Exception $e){
        return true;
    }
}


//Custom Money Number Format
function custom_number($number,$decimals = 2)
{
    return number_format($number,$decimals,'.',false);
}


/**
 * @return string
 */
function zero()
{
    return custom_number(0);
}

/**
 * @param string $message
 * @param $info
 * @return JsonResponse
 */
function mobile_info(string $message, $info)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
       'info'=> $info,
    ]);
}

/**
 * @param string $message
 * @return JsonResponse
 */
function mobile_success(string $message)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
    ]);
}

/**
 * @param string $message
 * @param int $status_code
 * @return mixed
 */
function mobile_error(string $message, int $status_code = 400)
{
    throw new HttpResponseException(response()->json([
        'status'=> false,
        'message'=> $message,
    ],$status_code));
}

/**
 * @param string $message
 * @param $info
 * @return JsonResponse
 */
function dashboard_info(string $message, $info)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
       'info'=> $info,
    ]);
}

/**
 * @param string $message
 * @return JsonResponse
 */
function dashboard_success(string $message)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
    ]);
}

/**
 * @param string $message
 * @param int $status_code
 * @return mixed
 */
function dashboard_error(string $message, int $status_code = 400)
{
    throw new HttpResponseException(response()->json([
        'status'=> false,
        'message'=> $message,
    ],$status_code));
}

/**
 * @param string $message
 * @param $info
 * @return JsonResponse
 */
function response_info(string $message, $info)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
       'info'=> $info,
    ]);
}

/**
 * @param string $message
 * @return JsonResponse
 */
function response_success(string $message)
{
    return response()->json([
       'status'=> true,
       'message'=> $message,
    ]);
}

/**
 * @param string $message
 * @param int $status_code
 * @return mixed
 */
function response_error(string $message, int $status_code = 400)
{
    throw new HttpResponseException(response()->json([
        'status'=> false,
        'message'=> $message,
    ],$status_code));
}

/**
 * @param $value
 * @return bool
 */
function empty_value($value) : bool
{
    $trimmed = trim($value);
    return is_null($value) or (! in_array($value,BOOLEANS,true) and empty($trimmed));
}

/**
 * @param $value
 * @return bool
 */
function empty_graph_ql_value($value) : bool
{
    $trimmed = trim($value);
    return is_null($value) or in_array($trimmed,['null','undefined'],true) or (! in_array($value,BOOLEANS,true) and empty($trimmed));
}


/**
 * @param $date
 * @param bool $use_carbon_method
 * @param string $carbon_method
 * @param array $carbon_method_args
 * @param string $format
 * @return false|string
 */
function db_date($date, bool $use_carbon_method = false , string $carbon_method = 'startOfDay', array $carbon_method_args = [], string $format = 'Y-m-d H:i:s')
{
    try {
        $valid_date = Carbon::parse($date);
        return $use_carbon_method ? $valid_date->{$carbon_method}(...$carbon_method_args)->format($format) : $valid_date->format($format);
    }catch (\Exception $e){
        return false;
    }
}

/**
 * @param string $event
 * @param int|null $request_id
 * @param null $guard_model
 * @param string $guard
 * @return void
 */
function trace(string $event,int $request_id = null, $guard_model = null, string $guard = 'driver')
{
    try {
        $guard_model ??= @auth($guard)->user();
         @Trace::create([
            'guard'=> $guard,
            'guard_id'=> $guard_model['id'],
            'event'=> $event,
            'request_id'=> $request_id,
            'latitude'=> $guard_model['latitude'],
            'longitude'=> $guard_model['longitude'],
        ]);
    }catch (Exception $e){}
}

/**
 * @param string $event
 * @param int|null $request_id
 * @param Model $model
 * @param iterable $ids
 * @param string $guard
 * @return void
 */
function multiple_trace(string $event, ?int $request_id , Model $model, iterable $ids, string $guard = 'driver')
{
    try {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        @$model::query()->select(['id as guard_id','latitude','longitude'])->whereIn('id',$ids)->cursor()->map(fn ($record) =>
        [
            'event'=> $event,
            'request_id'=> $request_id,
            'guard'=> $guard,
            'guard_id'=> $record['guard_id'],
            'latitude'=> $record['latitude'],
            'longitude'=> $record['longitude'],
            'created_at'=> $now,
            'updated_at'=> $now,
        ]
        )->chunk(500)->each(fn($_500) => @Trace::query()->insert($_500->all()));
    }catch (Exception $e){}
}

function update_driver_wallet($type,$amount,...$ids)
{
    $driver_stats = DriverStats::query()->whereIn('driver_id',$ids);
    switch ($type) :
        case 'Wallet Deposit':
            $driver_stats->increment('wallet',$amount); break;
        case 'Wallet Withdraw':
        case 'Cashout':
            $driver_stats->decrement('wallet',$amount); break;
    endswitch;
    return $driver_stats->pluck('wallet','driver_id');
}
