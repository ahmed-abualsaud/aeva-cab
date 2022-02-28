<?php

namespace App\Repository\Eloquent\Mutations;

use App\Driver;
use App\CabRating;
use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CabRatingRepository extends BaseRepository
{

    public function __construct(CabRating $model)
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

        $avg = $this->model->where('driver_id', $rating->driver_id)
        ->whereNotNull('rating')->avg('rating');

        Driver::find($rating->driver_id)->update(['rating' => $avg]);

        return $rating;
    }
}
