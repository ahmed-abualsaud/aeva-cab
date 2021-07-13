<?php

namespace App\Repository\Eloquent\Mutations;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPasswordRepository
{
    use SendsPasswordResetEmails;

    public function invoke(array $args)
    {
        $response = Password::broker($args['type'])->sendResetLink(['email' => $args['email']]);
        if ($response == Password::RESET_LINK_SENT) {
            return [
                'status'  => true,
                'message' => trans($response),
            ];
        }
 
        return [
            'status'  => false,
            'message' => trans($response),
        ];
    }
}
