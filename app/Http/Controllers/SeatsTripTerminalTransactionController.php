<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeatsTripTerminalTransaction;

class SeatsTripTerminalTransactionController extends Controller
{
    public function create(Request $req) {

        try {

            $obj = json_decode(json_encode($req->obj));
    
            $data = [
                'trx_id' => $obj->id,
                'partner_id' => $obj->profile_id,
                'terminal_id' => $obj->terminal_id,
                'source' => $obj->api_source,
                'amount' => $obj->amount_cents,
                'status' => $this->status($obj),
                'created_at' => $obj->created_at,
            ];
    
            return SeatsTripTerminalTransaction::create($data);
            
        } catch (\Exception $e) {
            //
        }
    }

    protected function status($obj)
    {
        if(!($obj->pending) && !($obj->success))
    		return 'DECLINED';
    	
        if (!($obj->pending) && ($obj->success))
    		return 'SUCCESS';
    	
        return 'PENDING';
    }
}
