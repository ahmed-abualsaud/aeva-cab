<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait QueryGenerator
{
    public function upsert($table, $rows, array $update)
    {
        $columns = array_keys($rows[0]);

        $columnsString = implode('`,`', $columns);
        $values = $this->buildSQLValuesFrom($rows);
        $updates = $this->buildSQLUpdatesFrom($update);
        $params = Arr::flatten($rows);

        $query = "insert into {$table} (`{$columnsString}`) values {$values} on duplicate key update {$updates}";

        return DB::statement($query, $params);
    }

    protected function buildSQLValuesFrom(array $rows)
    {
        $values = collect($rows)->reduce(function ($valuesString, $row) {
            return $valuesString .= '(' . rtrim(str_repeat("?,", count($row)), ',') . '),';
        }, '');

        return rtrim($values, ',');
    }


    protected function buildSQLUpdatesFrom(array $update)
    {
        $updateString = collect($update)->reduce(function ($updates, $column) {
            return $updates .= "`{$column}`=VALUES(`{$column}`),";
        }, '');

        return trim($updateString, ',');
    }
}