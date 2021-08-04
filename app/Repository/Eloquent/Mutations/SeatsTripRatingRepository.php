<?php

namespace App\Repository\Eloquent\Mutations;

use App\SeatsTripRating;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class SeatsTripRatingRepository extends BaseRepository
{

    public function __construct(SeatsTripRating $model)
    {
        parent::__construct($model);
    }
    
    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'directive'])->toArray();

        try {
            $rating = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new \Exception(__('lang.rating_not_found'));
        }

        $rating->update($input);

        return $rating;
    }
}
