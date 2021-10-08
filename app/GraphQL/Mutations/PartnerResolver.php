<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\PartnerRepositoryInterface;


class PartnerResolver
{
    private $partnerRepository;

    public function  __construct(PartnerRepositoryInterface $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->partnerRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->partnerRepository->update($args);
    }

    public function login($_, array $args)
    {
        return $this->partnerRepository->login($args);
    }

    public function assignDriver($_, array $args)
    {
        return $this->partnerRepository->assignDriver($args);
    }

    public function unassignDriver($_, array $args)
    {
        return $this->partnerRepository->unassignDriver($args);
    }

    public function assignUser($_, array $args)
    {
        return $this->partnerRepository->assignUser($args);
    }

    public function unassignUser($_, array $args)
    {
        return $this->partnerRepository->unassignUser($args);
    }

    public function updatePassword($_, array $args)
    {
        return $this->partnerRepository->updatePassword($args);
    }

    public function destroy($_, array $args)
    {
        return $this->partnerRepository->destroy($args);
    }
}