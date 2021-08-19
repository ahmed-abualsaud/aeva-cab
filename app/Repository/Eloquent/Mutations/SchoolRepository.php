<?php

namespace App\Repository\Eloquent\Mutations;

use App\School;
use App\Repository\Eloquent\BaseRepository;

class SchoolRepository extends BaseRepository
{
    public function __construct(School $model)
    {
        parent::__construct($model);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();    
    }
}
