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
                'source' => $this->getSource($obj),
                'amount' => $obj->amount_cents/100,
                'status' => $this->getStatus($obj),
                'created_at' => $obj->created_at,
            ];
    
            return SeatsTripTerminalTransaction::create($data);
            
        } catch (\Exception $e) {
            //
        }
    }

    protected function getStatus($obj)
    {
        if(!($obj->pending) && !($obj->success))
    		return 'DECLINED';
    	
        if (!($obj->pending) && ($obj->success))
    		return 'SUCCESS';
    	
        return 'PENDING';
    }

    protected function getSource($obj)
    {
        try {
            return $obj->source_data->type;
        } catch (\Exception $e) {
            return $obj->api_source;
        }
    }
}
