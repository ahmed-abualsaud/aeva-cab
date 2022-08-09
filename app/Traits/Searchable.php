<?php

namespace App\Traits;

trait Searchable
{
    protected function search($searchFor, $searchQuery, $result)
    {
        $q = '%' . $searchQuery . '%';

        if (str_contains($searchFor, '.')) {
            list($model, $field) = preg_split('~\.(?=[^.]*$)~', $searchFor);
            $result->whereHas($model, function($query) use ($field, $q) {
                $query->where($field, 'like', $q);
            })->with($model);
        } else {
            $result->where($searchFor, 'like', $q);
        }

        return $result;
    }
}
