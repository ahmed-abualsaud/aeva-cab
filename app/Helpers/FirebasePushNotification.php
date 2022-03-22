<?php

namespace App\Helpers;

class FirebasePushNotification
{
    public static function push($token, $title, $message, $data = null)
    {
        $API_ACCESS_KEY = config('custom.firebase_access_key');

        $payload = ['title' => $title, 'body' => $message];

        $fields = [
            'notification' => $payload,
            'data' => $payload,
            'priority' => "high"
        ];

        $fields['notification']['sound'] = 'default';

        if (is_array($token)) {
            $fields['registration_ids'] = $token;
        } else {
            $fields['to'] = $token;
        }

        if ($data) {
            $fields['data'] = array_merge($fields['data'], $data);
            $fields['notification'] = array_merge($fields['notification'], $data);
        }
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