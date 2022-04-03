<?php

namespace Qruz\Cab\Domain\Repository;

/**
* Interface EloquentRepositoryInterface
* @package App\Repositories
*/
interface EloquentRepositoryInterface
{
    public function create(array $args);
    public function update(array $args);
    public function login(array $args);
    public function updatePassword(array $args);
    public function destroy(array $args);
    public function invoke(array $args);
    public function updateRoute(array $args);
    public function changeStatus(array $args);
}