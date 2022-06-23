<?php

namespace Aeva\Cab\Application\Http\Controllers\Mutations;

use App\Driver;
use App\Settings;
use App\DriverStats;

use Aeva\Cab\Domain\Models\CabRequestTransaction;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CabRequestTransactionController 
{
    public function confirmCashout(Request $req) 
    {
        $params = $req->all();
        $server_key = Settings::select('name', 'value')->where('name', 'Aeva Mobility Server Key')->first()->value;
        $str = $server_key.$params['phone'];
        $hashed_str = hash("sha256",$str,true);
        $encoded_str = base64_encode($hashed_str);

        if($req->header('x-api-key') != $encoded_str) {
            $response = [
                'success' => false,
                'message' => 'Unauthorized'
            ];
            return response()->json($response, 401);
        }

        try {
            $driver = Driver::where('phone', $params['phone'])->firstOrFail();
            CabRequestTransaction::create([
                'driver_id' => $driver->id, 
                'costs' => $params['amount'],
                'payment_method' => 'Cashout',
                'uuid' => Str::orderedUuid()
            ]);

            DriverStats::where('driver_id', $driver->id)->update([
                'wallet' => DB::raw('wallet - '.$params['amount']), 
                'earnings' => DB::raw('earnings - '.$params['amount'])
            ]);

            $response = [
                'success' => true,
                'message' => 'Cashout Process Confirmed Successfully'
            ];
            return $response;
       } catch (ModelNotFoundException $e) {
            $response = [
                'success' => false,
                'message' => 'Not Found'
            ];
            return response()->json($response, 404);
       }
    }
}