<?php

namespace App\Repository\Mutations;

interface PaymentRepositoryInterface
{
    public function addCard(array $args);
    public function resendCode(array $args);
    public function validateOTP(array $args);
    public function makePayment(array $args);
    public function sessionRetrieve(array $args);
}