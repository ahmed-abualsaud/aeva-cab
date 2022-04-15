<?php

namespace App\Repository\Eloquent\Queries;   

use App\Repository\Queries\MainRepositoryInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Document;

class DocumentRepository extends BaseRepository implements MainRepositoryInterface
{
   public function __construct(Document $model)
   {
        parent::__construct($model);
   }

   public function invoke(array $args)
   {
        $documents = $this->model->where('documentable_id', $args['documentable_id'])
        ->where('documentable_type', $args['documentable_type'])
        ->get();

        return $documents;
   }

   public function driverEmptyDocuments(array $args) 
   {
          return $this->model
               ->where('documentable_id', $args['driver_id'])
               ->where('documentable_type', 'App\\Driver')
               ->whereNull('status')
               ->get();
   }
}