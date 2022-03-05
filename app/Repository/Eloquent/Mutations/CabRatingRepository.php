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
        $input = collect($args)->except(['id', 'request_id', 'directive'])->toArray();

        if (array_key_exists('id', $args) && $args['id'] != null) {
            try {
                $rating = $this->model->findOrFail($args['id']);
            } catch (ModelNotFoundException $e) {
                throw new \Exception(__('lang.rating_not_found'));
            }
        }
        if (array_key_exists('request_id', $args) && $args['request_id'] != null) {
            $rating = $this->model->where('request_id', $args['request_id'])->first();
        }

        $rating->update($input);

        $avg = $this->model->where('driver_id', $rating->driver_id)
        ->whereNotNull('rating')->avg('rating');

        Driver::find($rating->driver_id)->update(['rating' => $avg]);

        return $rating;
    }
}
