<?php

namespace App\Traits;

use Carbon\Carbon;

trait Filterable
{
    protected function dateFilter($period, $result, $field)
    {
        switch($period) {
            case 'today':
                return $result->whereDate($field, '=', Carbon::today());

            case 'yesterday':
                return $result->whereDate($field, '=', Carbon::yesterday());
                
            case 'week':
                return $result->whereDate($field, '>=', Carbon::now()->subDays(7));
            
            case 'month':
                return $result->whereDate($field, '>=', Carbon::now()->subMonth());
            
            case 'quarter':
                return $result->whereDate($field, '>=', Carbon::now()->subMonth(3));
            
            case 'half':
                return $result->whereDate($field, '>=', Carbon::now()->subMonth(6));
            
            case 'year':
                return $result->whereDate($field, '>=', Carbon::now()->subMonth(12)); 
                
            default:
                if (str_contains($period, ',')) {
                    list($from, $to) = explode(',', $period);
                    return $result->whereBetween($field, [$from, $to]);
                } else {
                    return $result->whereDate($field, '=', $period);
                }
        }
    }
}