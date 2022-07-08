<?php

namespace App\Repository\Eloquent\Mutations;

use App\Vehicle;
use App\Document;
use App\DriverVehicle;

use App\Traits\HandleUpload;
use App\Repository\Eloquent\BaseRepository;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VehicleRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Vehicle $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $input = collect($args)->except(['directive', 'photo', 'driver_id', 'car_image_document', 'car_check_document', 'back_car_license_document', 'front_car_license_document'])->toArray();

        if (array_key_exists('photo', $args) && $args['photo']) {
          $url = $this->uploadOneFile($args['photo'], 'images');
          $input['photo'] = $url;
        }

        $vehicle = $this->model->create($input);

        $status = 'Approved';
        if (array_key_exists('driver_id', $args) && $args['driver_id']) {
            DriverVehicle::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $args['driver_id'],
                'active' => false
            ]);
            $status = null;
        }

        $row = [
            'status' => $status,
            'documentable_id' => $vehicle->id,
            'documentable_type' =>'App\\Vehicle',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];


        for ($i = 0; $i < 4; $i++) {$rows[] = $row;}

        $rows[0]['name'] = 'فحص السيارة';
        $rows[1]['name'] = 'صورة السيارة';
        $rows[2]['name'] = 'رخصة سيارة سارية:اﻷمام';
        $rows[3]['name'] = 'رخصة سيارة سارية:الخلف';
        
        $rows[0]['url'] = $this->uploadOneFile($args['car_check_document'], 'documents');
        $rows[1]['url'] = $this->uploadOneFile($args['car_image_document'], 'documents');
        $rows[2]['url'] = $this->uploadOneFile($args['front_car_license_document'], 'documents');
        $rows[3]['url'] = $this->uploadOneFile($args['back_car_license_document'], 'documents');

        Document::insert($rows);

        return $vehicle;
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive', 'photo'])->toArray();

        try {
            $vehicle = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.vehicle_not_found'));
        }

        if (array_key_exists('photo', $args) && $args['photo']) {
            if ($vehicle->photo) $this->deleteOneFile($vehicle->photo, 'images');
            $url = $this->uploadOneFile($args['photo'], 'images');
            $input['photo'] = $url;
        }

        $vehicle->update($input);

        if (!($vehicle->license_plate && $vehicle->car_make_id && $vehicle->car_model_id)) {
            $vehicle->update(['approved' => false]);
        }

        return $vehicle;
    }

    public function activateVehicle(array $args) 
    {
        $vehicle = $this->model->join('driver_vehicles', 'driver_vehicles.vehicle_id', '=', 'vehicles.id')
            ->where('driver_vehicles.driver_id', $args['driver_id'])
            ->where('driver_vehicles.vehicle_id', $args['vehicle_id'])
            ->where('vehicles.approved', true);

        $ret = $vehicle->first();
        if ($ret) {
            DB::table('driver_vehicles')
                ->where('driver_vehicles.driver_id', $args['driver_id'])
                ->where('active', true)
                ->update(['active' => false]);

            $vehicle->update(['active' => true]);
        }

        return $ret;
    }
}
