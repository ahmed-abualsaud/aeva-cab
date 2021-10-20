<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\FollowerRepository;
 
class FollowerResolver
{
    private $followerRepository;

    public function __construct(FollowerRepository $followerRepository)
    {
        $this->followerRepository = $followerRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->followerRepository->create($args);
    }

    public function destroy($_, array $args)
    {
        return $this->followerRepository->destroy($args);
    }
}