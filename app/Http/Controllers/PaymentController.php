<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function view()
    {
        return view('pay');
    }

    public function pay(Request $request)
    {
        try {
            $user = auth('user')->user();
            $postData = [
                'sessionId' => $request->session_id,
                'mobileNumber' => preg_replace('/^(002|\+2)/', '', $user->phone),
                'email' => $user->email,
                'amount' => $request->amount
            ];
            $output = $this->output($postData, 'session/pay');
            $res = json_decode($output);
            if ($res->statusCode == 200) {
                $user->wallet_balance += $request->amount;
                $user->save();
            }
            return response()->json([
                "statusCode" => $res->statusCode,
                "message" => $res->message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "statusCode" => 500,
                "message" => 'Something went wrong'
            ]);
        }

    }

    protected function output(array $postData, string $endpoint)
    {
        $secureHash = config('custom.valulus_hash_secret');
        $postData['hashSecret'] = $this->generateHash($secureHash, $postData);
        $postData['appId'] = config('custom.valulus_app_id');
        $postData['password'] = config('custom.valulus_password');
        $url = 'https://api.vapulus.com:1338/app/'.$endpoint;
        
        
        return $this->HTTPPost($url, $postData);
    }

    protected function generateHash($hashSecret, $postData)
    {
        ksort($postData);
        $message="";
        $appendAmp=0;
        foreach($postData as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $message .= $key . '=' . $value;
                    $appendAmp = 1;
                } else {
                    $message .= '&' . $key . "=" . $value;
                }
            }
        }

        $secret = pack('H*', $hashSecret);
        return hash_hmac('sha256', $message, $secret);
    }

    protected function HTTPPost($url, array $params)
	{
		$query = http_build_query($params);
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
	}
}
