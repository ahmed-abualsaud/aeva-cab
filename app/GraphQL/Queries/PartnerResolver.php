<?php

namespace App\GraphQL\Queries;

use App\User;
use App\Driver;
use App\PartnerDriver;

class PartnerResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function users($_, array $args)
    {
        $partnerUsers = User::Join('partner_users', 'partner_users.user_id', '=', 'users.id')
            ->where('partner_users.partner_id', $args['partner_id'])
            ->get();

        return $partnerUsers;
    }
}
