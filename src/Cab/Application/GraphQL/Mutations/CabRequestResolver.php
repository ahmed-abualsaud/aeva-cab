<?php

namespace Aeva\Cab\Application\GraphQL\Mutations;

use Aeva\Cab\Domain\Repository\Eloquent\Mutations\CabRequestRepository;

class CabRequestResolver
{
    private $cabRequestRepository;

    public function __construct(CabRequestRepository $cabRequestRepository)
    {
        $this->cabRequestRepository = $cabRequestRepository;
    }

    public function schedule($_, array $args)
    {
        return $this->cabRequestRepository->schedule($args);
    }

    public function search($_, array $args)
    {
        return $this->cabRequestRepository->search($args);
    }

    public function send($_, array $args)
    {
        return $this->cabRequestRepository->send($args);
    }

    public function accept($_, array $args)
    {
        return $this->cabRequestRepository->accept($args);
    }

    public function arrived($_, array $args)
    {
        return $this->cabRequestRepository->arrived($args);
    }

    public function start($_, array $args)
    {
        return $this->cabRequestRepository->start($args);
    }

    public function end($_, array $args)
    {
        return $this->cabRequestRepository->end($args);
    }

    public function cancel($_, array $args)
    {
        return $this->cabRequestRepository->cancel($args);
    }

    public function reset($_, array $args)
    {
        return $this->cabRequestRepository->reset($args);
    }

    public function redirect($_, array $args)
    {
        return $this->cabRequestRepository->redirect($args);
    }

    public function updateDriverCabStatus($_, array $args)
    {
        return $this->cabRequestRepository->updateDriverCabStatus($args);
    }
}