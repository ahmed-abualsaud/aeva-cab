<?php

namespace App\Repository\Eloquent\Mutations;

use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ResetPasswordRepository
{
    use ResetsPasswords;
    use ValidatesRequests;

    public function invoke(array $args)
    {
        $input = collect($args)->except(['directive', 'type'])->toArray();
        $response = Password::broker($args['type'])->reset($input, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }); 

        if ($response === Password::PASSWORD_RESET) {
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

    public function withOtp(array $args)
    {
        try {
            return $args['model']::where('phone', $args['phone'])
                ->firstOrFail()
                ->update([
                    'password' => Hash::make($args['password'])
                ]);
        } catch(\Exception $e) {
            throw new CustomException(
                __('lang.password_not_changed'),
                'customValidation'
            );
        }
    }
}