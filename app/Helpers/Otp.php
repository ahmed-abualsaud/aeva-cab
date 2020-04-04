<?php

namespace App\Helpers;

class Otp
{
    public static function send($to, $message)
    {
        $OTP_USERNAME = env('OTP_USERNAME');
        $OTP_PASSWORD = env('OTP_PASSWORD');
        $OTP_SENDER_ID = env('OTP_SENDER_ID');
        
        $ch = curl_init();
        $msg = curl_escape($ch, $message);
        $url = "https://smsmisr.com/api/webapi/?username=".$OTP_USERNAME."&password=".$OTP_PASSWORD."&language=1&sender=".$OTP_SENDER_ID."&mobile=".$to."&message=".$msg;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}