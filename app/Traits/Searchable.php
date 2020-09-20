<?php

namespace App\Traits;

trait Searchable
{
    protected function search($searchFor, $searchQuery, $result)
    {
        $q = '%' . $searchQuery . '%';
        return $result->where($searchFor, 'like', $q); 
    }
}