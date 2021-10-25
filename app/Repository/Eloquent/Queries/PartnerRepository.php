<?php

namespace App\Repository\Eloquent\Queries;

use App\User;
use App\Repository\Queries\PartnerRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function users(array $args) 
    {
        $partnerUsers = $this->user->Join('partner_users', 'partner_users.user_id', '=', 'users.id')
        ->where('partner_users.partner_id', $args['partner_id'])
        ->get();

        return $partnerUsers;
    }
}