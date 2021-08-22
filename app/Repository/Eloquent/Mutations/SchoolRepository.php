<?php

namespace App\Repository\Eloquent\Mutations;

use App\School;
use App\SchoolRequest;
use App\Repository\Eloquent\BaseRepository;

class SchoolRepository extends BaseRepository
{
    public function __construct(School $model)
    {
        parent::__construct($model);
    }

    public function destroy(array $args)
    {
        SchoolRequest::whereIn('school_id', $args['id'])
            ->update(['status' => 'INCOMPLETE']);
            
        return $this->model->whereIn('id', $args['id'])->delete();          
    }
}
