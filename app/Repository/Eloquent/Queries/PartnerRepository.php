<?php

namespace App\Repository\Eloquent\Queries;

use App\Partner;
use App\User;
use App\Repository\Queries\PartnerRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    private $user;

    public function __construct(Partner $model, User $user)
    {
        parent::__construct($model);
        $this->user = $user;
    }

    public function users(array $args) 
    {
        $partnerUsers = $this->user->Join('partner_users', 'partner_users.user_id', '=', 'users.id')
        ->where('partner_users.partner_id', $args['partner_id'])
        ->get();

        return $partnerUsers;
    }

    public function partnerPaymentCategories(array $args)
    {
        return $this->model->select('payment_categories')
            ->findOrFail($args['partner_id'])
            ->payment_categories;
    }
}