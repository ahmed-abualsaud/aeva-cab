<?php

namespace App\Repository\Eloquent\Queries;   

use App\User;
use App\Driver;
use App\PartnerDriver;
use App\Repository\Queries\PartnerRepositoryInterface;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function users(array $args) 
    {
        $partnerUsers = $this->model->Join('partner_users', 'partner_users.user_id', '=', 'users.id')
        ->where('partner_users.partner_id', $args['partner_id'])
        ->get();

        return $partnerUsers;
    }
}