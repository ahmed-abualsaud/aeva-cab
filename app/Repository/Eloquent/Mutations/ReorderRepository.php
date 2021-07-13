<?php

namespace App\Repository\Eloquent\Mutations;


class ReorderRepository
{
    public function invoke(array $args)
    {
        return $args['model']::reorder($args['orders']);
    }
}