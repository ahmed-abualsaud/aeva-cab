<?php

namespace App\Traits;

use Carbon\Carbon;

trait Filterable
{
    protected function dateFilter($period, $result, $field)
    {
        switch($period) {
            case 'today':
                return $result->where($field, '>=', Carbon::today());
            
            case 'week':
                return $result->where($field, '>=', Carbon::now()->subDays(7));
            
            case 'month':
                return $result->where($field, '>=', Carbon::now()->subMonth());
            
            case 'quarter':
                return $result->where($field, '>=', Carbon::now()->subMonth(3));
            
            case 'half':
                return $result->where($field, '>=', Carbon::now()->subMonth(6));
            
            case 'year':
                return $result->where($field, '>=', Carbon::now()->subMonth(12));  
        }
    }
}