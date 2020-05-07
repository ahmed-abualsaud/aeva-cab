<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateFilter
{
    protected function dateFilter($period, $result, $field)
    {
        switch($period) {
            case $period == 'today':
                return $result->where($field, '>=', Carbon::today());
            
            case $period == 'week':
                return $result->where($field, '>=', Carbon::now()->subDays(7));
            
            case $period == 'month':
                return $result->where($field, '>=', Carbon::now()->subMonth());
            
            case $period == 'quarter':
                return $result->where($field, '>=', Carbon::now()->subMonth(3));
            
            case $period == 'half':
                return $result->where($field, '>=', Carbon::now()->subMonth(6));
            
            case $period == 'year':
                return $result->where($field, '>=', Carbon::now()->subMonth(12));  
        }
    }
}