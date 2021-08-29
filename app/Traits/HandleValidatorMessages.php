<?php

namespace App\Traits;

trait HandleValidatorMessages
{
    public static function handleValidatorMessages($errors)
    {
        $errors =  json_decode($errors, true);
        $messages['messages'] = [];

        $errors =  array_values($errors);

        array_walk_recursive($errors, function($val) use (&$messages) {
            $messages['messages'][] = $val;
        });

        return $messages;
    }

    
}