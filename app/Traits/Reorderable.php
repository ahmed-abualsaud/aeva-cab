<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

trait Reorderable
{
    public static function handleReorder(string $table, array $orders)
    {
        try {
            $cases = []; $ids = []; $order = [];

            foreach ($orders as $value) {
                $id = (int) $value['id'];
                $cases[] = "WHEN {$id} then ?";
                $order[] = $value['order'];
                $ids[] = $id;
            }

            $ids = implode(',', $ids);
            $cases = implode(' ', $cases);
            $params = $order;
            $params[] = date("Y-m-d H:i:s");

            return DB::update("UPDATE `{$table}` SET 
                `order` = CASE `id` {$cases} END,
                `updated_at` = ? 
                WHERE `id` in ({$ids})", $params);

        } catch (\Exception $e) {
            throw new CustomException(__('lang.update_failed'));
        }
    }

    
}