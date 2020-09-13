<?php

namespace App\GraphQL\Mutations;

use App\Card;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentResolver 
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function addCard($_, array $args)
    {
        try {
            $postData = [
                'cardNum' =>  $args['card_num'],
                'cardExp' =>  $args['card_exp_y'].$args['card_exp_m'],
                'cardCVC' =>  $args['card_cvc'],
                'holderName' => $args['holder_name'],
                'mobileNumber' => $args['mobile_number'],
                'email' => $args['email']
            ];
            $output = $this->output($postData, 'addCard');
            $res = json_decode($output);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to add this card.'.$e->getMessage());
        }

        try {
            $exp_date = '20'.$args['card_exp_y'].'-'.$args['card_exp_m'];
            Card::create([
                'user_id' => $args['user_id'],
                'holder_name' => $args['holder_name'],
                'card_exp' => Carbon::parse($exp_date)->endOfMonth()->toDateString(),
                'last_four' => substr($args['card_num'], -4),
                'payer_id' => $res->userId,
                'card_id' => $res->cardId,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('We could not able to add this card.'.$e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Payment card added successfully."
        ];

    }

    public function resendCode($_, array $args)
    {
        $card = $this->getCard($args['card_id']);

        try {
            $postData = [
                'userId' => $card->payer_id,
                'cardId' => $card->card_id,
            ];
            $output = $this->output($postData, 'resendCode');
        } catch (\Exception $e) {
            throw new \Exception('We could not able to resend the code.'.$e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Validation code resent successfully."
        ];

    }

    public function validateOTP($_, array $args)
    {
        $card = $this->getCard($args['card_id']);

        try {
            $postData = [
                'userId' => $card->payer_id,
                'cardId' => $card->card_id,
                'validationCode' => $args['validation_code']
            ];
            $output = $this->output($postData, 'validateOTP');
        } catch (\Exception $e) {
            throw new \Exception('We could not able to validate the OTP.'.$e->getMessage());
        }

        return [
            "status" => true,
            "message" => "OTP validated successfully."
        ];

    }

    public function makePayment($_, array $args)
    {
        $card = $this->getCard($args['card_id']);

        try {
            $postData = [
                'userId' => $card->payer_id,
                'cardId' => $card->card_id,
                'amount' => $args['amount']
            ];
            
            $output = $this->output($postData, 'makePayment');
        } catch (\Exception $e) {
            throw new \Exception('We could not able to process this payment.'.$e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Ok"
        ];

    }

    public function sessionRetrieve($_, array $args)
    {
        try {
            $postData = ['sessionId' => $args['session_id']];
            $output = $this->output($postData, 'session/retrieve');
        } catch (\Exception $e) {
            throw new \Exception('We could not able to process this payment.'.$e->getMessage());
        }

        return [
            "status" => true,
            "message" => "Done"
        ];

    }

    protected function getCard($id)
    {
        try {
            $card = Card::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return [
                "status" => false,
                "message" => "Card not found."
            ];
        }

        return $card;
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