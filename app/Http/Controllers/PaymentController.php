<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\SendPushController;

use Auth;
use Exception;

use App\Card;
use App\User;
use App\WalletPassbook;
use App\UserRequest;
use App\UserRequestPayment;

class PaymentController extends Controller
{
    /**
     * payment for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Request $request)
    {
        //
    }


    /**
     * add wallet money for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function add_money(Request $request)
    {
        //
    }
}
