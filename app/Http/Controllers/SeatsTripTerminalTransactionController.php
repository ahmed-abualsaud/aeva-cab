<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeatsTripTerminalTransaction;
use App\Partner;

class SeatsTripTerminalTransactionController extends Controller
{
    public function create(Request $req) {

        $obj = json_decode(json_encode($req->obj));

        //return response()->json($obj);
    	if(!($obj->pending) && !($obj->success)){
    		$status = 'DECLINED';
    	}

    	else if(!($obj->pending) && ($obj->success)) {
    		$status = 'SUCCESS';
    	}

    	else $status = 'PENDING';

    	return SeatsTripTerminalTransaction::create([
    		'trnx_id'     => $obj->id,
    		'partner_id'  => $obj->profile_id,
            'terminal_id' => $obj->terminal_id,
    		'source'      => $obj->api_source,
    		'amount'      => $obj->amount_cents,
    		'status'      => $status,
            'created_at'  => $obj->created_at,
    	]);
    }
}
