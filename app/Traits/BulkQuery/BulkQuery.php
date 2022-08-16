<?php


namespace App\Traits\BulkQuery;


use Illuminate\Database\Eloquent\Model;

class BulkQuery
{
    public static function update($table,array $columns,array $updates_data_keyed_by_id)
    {
        $update = new Update($table);
        $update->useColumns($columns);
        $update->prepare($updates_data_keyed_by_id);
        $update->execute();
    }

//
//    public static function insert($table,array $columns,array $inserted_data)
//    {
//        $insert = new Insert($table);
//        $insert->useColumns($columns);
//        $insert->prepare($inserted_data);
//        $insert->execute();
//    }


    public static function insert(Model $model,array $inserted_data)
    {
        $model->query()->insert($inserted_data);
    }
}
