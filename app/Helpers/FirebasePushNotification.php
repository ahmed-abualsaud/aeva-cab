<?php

namespace App\Helpers;

class FirebasePushNotification
{
    public static function push($token, $title, $message, $data = false)
    {
        $API_ACCESS_KEY = config('custom.firebase_access_key');

        $fields = [
            'registration_ids' => $token,
            'notification' => [
                'title' => $title,
                'body' => $message,
                'sound'     => "default"
            ],
            "priority" => "high"
        ];
        
        if ($data) $fields['data'] = $data;

        $headers = [
            'Authorization: key=' . $API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec( $ch );
        curl_close( $ch );

        return $result;
    }
}