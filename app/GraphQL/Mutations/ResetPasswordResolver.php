<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\ResetPasswordRepository;

class ResetPasswordResolver
{
    private $resetPasswordRepository;

    public function __construct(ResetPasswordRepository $resetPasswordRepository)
    {
        $this->resetPasswordRepository = $resetPasswordRepository;
    }

    public function __invoke($_, array $args)
    {
        return $this->resetPasswordRepository->invoke($args);
    }

    public function withOtp($_, array $args)
    {
        return $this->resetPasswordRepository->withOtp($args);
    }
}
