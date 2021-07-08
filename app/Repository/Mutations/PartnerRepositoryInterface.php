<?php

namespace App\Repository\Mutations;

interface PartnerRepositoryInterface
{
    public function assignDriver(array $args);
    public function unassignDriver(array $args);
    public function assignUser(array $args);
    public function unassignUser(array $args);
}