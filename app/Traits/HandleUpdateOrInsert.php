<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait HandleUpdateOrInsert
{
    public static function updateOrInsert($table, $rows, array $update)
    {
        $columns = self::buildSQLColumnsFrom($rows);
        $values = self::buildSQLValuesFrom($rows);
        $updates = self::buildSQLUpdatesFrom($update);
        $params = Arr::flatten($rows);

        $query = "insert into {$table} (`{$columns}`) values {$values} on duplicate key update {$updates}";

        return DB::statement($query, $params);
    }

    protected static function buildSQLColumnsFrom(array $rows) 
    {
        if (count($rows) === count($rows, COUNT_RECURSIVE)) {
            $columns = array_keys($rows);
        } else {
            $columns = array_keys($rows[0]);
        }

        return implode('`,`', $columns);
    }

    protected static function buildSQLValuesFrom(array $rows)
    {
        if (count($rows) == count($rows, COUNT_RECURSIVE)) 
            return '(' . rtrim(str_repeat("?,", count($rows)), ',') . ')';

        $values = collect($rows)->reduce(function ($valuesString, $row) {
            return $valuesString .= '(' . rtrim(str_repeat("?,", count($row)), ',') . '),';
        }, '');

        return rtrim($values, ',');
    }


    protected static function buildSQLUpdatesFrom(array $update)
    {
        $updateString = collect($update)->reduce(function ($updates, $column) {
            return $updates .= "`{$column}`=VALUES(`{$column}`),";
        }, '');

        return trim($updateString, ',');
    }
}