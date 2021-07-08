<?php

namespace App\GraphQL\Mutations;

use App\Repository\Mutations\PaymentRepositoryInterface;

class PaymentResolver 
{
    private $paymentRepository;

    public function  __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function addCard($_, array $args)
    {
        return $this->paymentRepository->addCard($args);
    }

    public function resendCode($_, array $args)
    {
        return $this->paymentRepository->resendCode($args);
    }

    public function validateOTP($_, array $args)
    {
        return $this->paymentRepository->validateOTP($args);
    }

    public function makePayment($_, array $args)
    {
        return $this->paymentRepository->makePayment($args);
    }

    public function sessionRetrieve($_, array $args)
    {
        return $this->paymentRepository->sessionRetrieve($args);
    }
}