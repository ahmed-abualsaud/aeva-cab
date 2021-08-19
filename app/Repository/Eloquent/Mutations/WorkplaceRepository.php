<?php

namespace App\Repository\Eloquent\Mutations;

use App\Workplace;
use App\Repository\Eloquent\BaseRepository;

class WorkplaceRepository extends BaseRepository
{
    public function __construct(Workplace $model)
    {
        parent::__construct($model);
    }

    public function destroy(array $args)
    {
        return $this->model->whereIn('id', $args['id'])->delete();    
    }
}
