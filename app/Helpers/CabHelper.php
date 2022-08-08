<?php /** @noinspection PhpMissingReturnTypeInspection */

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
 * @param string|null $value
 * @return bool
 */
function empty_value(?string $value) : bool
{
    return is_null($value) or (! is_numeric($value = trim($value)) and empty($value));
}


/**
 * @param string|null $value
 * @return bool
 */
function empty_graph_ql_value(?string $value) : bool
{
    return is_null($value) or in_array($value = trim($value),['null','undefined'],true) or (! is_numeric($value) and empty($value));
}

