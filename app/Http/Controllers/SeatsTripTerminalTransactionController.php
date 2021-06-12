<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SeatsTripTerminalTransaction;

class SeatsTripTerminalTransactionController extends Controller
{
    public function create(Request $req) {

        $obj = json_decode(json_encode($req->obj));
        $ord = json_decode(json_encode($obj->order));

    	if(!($obj->pending) && !($obj->success)){
    		$status = 'Declined';
    	}

    	else if(!($obj->pending) && ($obj->success)) {
    		$status = 'Successfull';
    	}

    	else $status = 'Pending';

    	return SeatsTripTerminalTransaction::create([
    		'trnx_id'        => $obj->id,
    		'order_id'       => $ord->id,
    		'payment_method' => $ord->payment_method,
    		'amount'         => $obj->amount_cents,
    		'currency'       => $obj->currency,
    		'date_created'   => $obj->created_at,
    		'payment_source' => json_encode($obj->source_data),
    		'status'         => $status,
    	]);
    }
}
