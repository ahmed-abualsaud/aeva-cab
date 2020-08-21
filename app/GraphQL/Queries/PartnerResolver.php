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
    public function partnerAssignedDrivers($_, array $args)
    {
        $partnerDrivers = PartnerDriver::where('partner_id', $args['partner_id'])->pluck('driver_id');

        return Driver::whereIn('id', $partnerDrivers)->get();
    }

    public function users($_, array $args)
    {
        $partnerUsers = User::Join('partner_users', 'partner_users.user_id', '=', 'users.id')
            ->where('partner_users.partner_id', $args['partner_id'])
            ->selectRaw('users.*, partner_users.employee_id')
            ->get();

        return $partnerUsers;
    }
}
