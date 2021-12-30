<?php

namespace App\Repository\Mutations;

interface CabRequestRepositoryInterface
{
    public function schedule(array $args);
    public function search(array $args);
    public function accept(array $args);
    public function arrived(array $args);
    public function start(array $args);
    public function end(array $args);
    public function cancel(array $args);
}