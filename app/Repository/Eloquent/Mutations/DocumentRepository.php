<?php

namespace App\Repository\Eloquent\Mutations;

use App\Vehicle;
use App\Document;
use App\DriverVehicle;

use App\Traits\HandleUpload;
use App\Exceptions\CustomException;
use App\Repository\Eloquent\BaseRepository;

use Illuminate\Database\Eloquent\ModelNotFoundException;


class DocumentRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $file = $args['file'];
        $url = $this->uploadOneFile($file, 'documents');
        $input = collect($args)->except(['file', 'directive'])->toArray();
        $input['url'] = $url;
        
        if (!$input['name']) {
            $input['name'] = $file->getClientOriginalName();
        }

        $document = $this->model->create($input);

        return $document;
    }

    public function update(array $args)
    {
        try {
            $document = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.document_not_found'));
        }

        $input = collect($args)->except(['file', 'directive'])->toArray();

        if (array_key_exists('file', $args) && $args['file'] != null) {
            $file = $args['file'];
            if ($document->url) $this->deleteOneFile($document->url, 'documents');
            $url = $this->uploadOneFile($file, 'documents');
            $input['url'] = $url;
            $input['status'] = 'In review';
            
            if (array_key_exists('name', $args) && !$input['name']) {
                $input['name'] = $file->getClientOriginalName();
            }
        }

        $document->update($input);

        $this->checkVehicleAndDocumentsApproved($document);

        return $document;
    }

    public function destroy(array $args)
    {
        try {
            $document = $this->model->findOrFail($args['id']);
            $this->deleteOneFile($document->url, 'documents');
            $document->delete();
        } catch(ModelNotFoundException $e) {
            throw new \Exception(__('lang.document_not_found') . $e->getMessage());
        }

        return __('lang.document_deleted');
    }

    public function addVehicleWithDocuments(array $args)
    {
        $vehicle = Vehicle::create([
            'approved' => false,
            'text' => $args['text'],
            'car_type_id' => $args['car_type_id']
        ]);

        DriverVehicle::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $args['driver_id'],
            'active' => false
        ]);

        Document::createVehicleDocuments($vehicle->id);

        return $vehicle;
    }

    protected function checkVehicleAndDocumentsApproved($document)
    {
        $docsNames = [
            'فحص السيارة', 
            'صورة السيارة', 
            'رخصة سيارة سارية:اﻷمام', 
            'رخصة سيارة سارية:الخلف'
        ];

        $approvedDocs = $this->model
            ->where('documentable_id', $document->documentable_id)
            ->whereIn('name', $docsNames)
            ->where('status', 'Approved')
            ->count();

        if  (in_array($document->name, $docsNames) && ($approvedDocs == 4)) 
        {
            Vehicle::where('id', $document->documentable_id)
                    ->whereNotNull(['license_plate', 'car_model_id', 'car_make_id'])
                    ->update(['approved' => true]);
        }
    }
}
