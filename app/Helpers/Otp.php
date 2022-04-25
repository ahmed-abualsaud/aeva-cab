<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Otp
{
    public static function send($to, $message)
    {
        $username = config('custom.victorylink_username');
        $password = config('custom.victorylink_password');
        $url = config('custom.victorylink_url');
        $sender = config('custom.victorylink_sender');

        $request_body = [
            'UserName'=> $username,
            'Password'=> $password,
            'SMSText'=> $message,
            'SMSLang'=> 'A',
            'SMSSender'=> $sender,
            'SMSReceiver'=> $to,
            'SMSID'=> Str::orderedUuid(),
        ];

        return Http::asJson()->post($url, $request_body);
    }
}