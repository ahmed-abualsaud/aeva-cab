<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeatsTripTerminalTransaction;

class SeatsTripTerminalTransactionController extends Controller
{
    public function create(Request $req) {

        $obj = json_decode(json_encode($req->obj));
        $source = json_decode(json_encode($obj->source_data));

        //return response()->json($obj);
    	if(!($obj->pending) && !($obj->success)){
    		$status = 'Declined';
    	}

    	else if(!($obj->pending) && ($obj->success)) {
    		$status = 'Successfull';
    	}

    	else $status = 'Pending';

    	return SeatsTripTerminalTransaction::create([
    		'trnx_id'     => $obj->id,
    		'operator_id' => $obj->profile_id,
            'terminal_id' => $obj->terminal_id,
    		'api_source'  => $obj->api_source,
    		'amount'      => $obj->amount_cents,
    		'currency'    => $obj->currency,
    		'type'        => $source->type,
            'sub_type'    => $source->sub_type,
    		'status'      => $status,
            'created_at'  => $obj->created_at,
    	]);
    }
}
