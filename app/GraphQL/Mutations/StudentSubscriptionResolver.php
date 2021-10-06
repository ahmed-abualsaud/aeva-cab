<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\StudentSubscriptionRepository;
 
class StudentSubscriptionResolver
{
    private $studentSubscriptionRepository;

    public function __construct(StudentSubscriptionRepository $studentSubscriptionRepository)
    {
        $this->studentSubscriptionRepository = $studentSubscriptionRepository;
    }

    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function create($_, array $args)
    {
        return $this->studentSubscriptionRepository->create($args);
    }

    public function update($_, array $args)
    {
        return $this->studentSubscriptionRepository->update($args);
    }

    public function reschedule($_, array $args)
    {
        return $this->studentSubscriptionRepository->reschedule($args);
    }

    public function destroy($_, array $args)
    {
        return $this->studentSubscriptionRepository->destroy($args);
    }
}