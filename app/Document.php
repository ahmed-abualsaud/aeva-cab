<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $connection ='mysql';


    /**
     * Get the owning documentable model.
     */
    public function documentable()
    {
        return $this->setConnection('mysql')->morphTo();
    }

    public function admin()
    {
        return $this->setConnection('mysql2')->belongsTo(Admin::class);
    }

    public static function createDriverDocuments($driver_id)
    {
        $row = [
            'documentable_id' => $driver_id,
            'documentable_type' =>'App\\Driver',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($i = 0; $i < 6; $i++) {$rows[] = $row;}

        $rows[0]['name'] = 'سجل جنائي';
        $rows[1]['name'] = 'الصورة الشخصية';
        $rows[2]['name'] = 'اختبار المخدرات';
        $rows[3]['name'] = 'البطاقة الشخصية:اﻷمام';
        $rows[4]['name'] = 'البطاقة الشخصية:الخلف';
        $rows[5]['name'] = 'رخصة قيادة مصرية سارية';

        Document::insert($rows);

        return Document::where('documentable_type', 'App\\Driver')
            ->where('documentable_id', $driver_id)
            ->latest()->take(6)->get();

    }

    public static function createVehicleDocuments($vehicle_id)
    {
        $row = [
            'documentable_id' => $vehicle_id,
            'documentable_type' =>'App\\Vehicle',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        for ($i = 0; $i < 4; $i++) {$rows[] = $row;}

        $rows[0]['name'] = 'فحص السيارة';
        $rows[1]['name'] = 'صورة السيارة';
        $rows[2]['name'] = 'رخصة سيارة سارية:اﻷمام';
        $rows[3]['name'] = 'رخصة سيارة سارية:الخلف';

        Document::insert($rows);

        return Document::where('documentable_type', 'App\\Vehicle')
            ->where('documentable_id', $vehicle_id)
            ->latest()->take(4)->get();
    }
}
