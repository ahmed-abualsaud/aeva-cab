<?php

namespace Qruz\Cab\Domain\Repository\Eloquent\Mutations;

use App\Driver;

use Qruz\Cab\Domain\Models\CabRating;

use Qruz\Cab\Domain\Repository\Eloquent\BaseRepository;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class CabRatingRepository extends BaseRepository
{

    public function __construct(CabRating $model)
    {
        parent::__construct($model);
    }

    public function update(array $args)
    {
        $input = collect($args)->except(['id', 'request_id', 'user_id', 'driver_id', 'directive'])->toArray();
        $rating = null;
        
        if (array_key_exists('request_id', $args) && $args['request_id'] != null) 
        {
            $rating = $this->model->where('request_id', $args['request_id']);
        }

        if (array_key_exists('user_id', $args) && $args['user_id'] != null &&
                   array_key_exists('driver_rating', $args) && $args['driver_rating'] != null) 
        {
            $rating = $this->model->where('user_id', $args['user_id'])->whereNull('driver_rating');
        } 

        if (array_key_exists('driver_id', $args) && $args['driver_id'] != null &&
                   array_key_exists('user_rating', $args) && $args['user_rating'] != null) 
        {
            $rating = $this->model->where('driver_id', $args['driver_id'])->whereNull('user_rating');
        }

        if (!$rating) {
            throw new \Exception(__('lang.rating_not_found'));
        }

        $ids = $rating->get()->pluck('id');

        if ($rating->update($input) && array_key_exists('driver_rating', $args) && $args['driver_rating'] != null) {
            $this->updateDriversTotalRatings();
        }

        return $this->model->whereIn('id', $ids)->get();
    }

    private function updateDriversTotalRatings() 
    {
        $avgs = $this->model->selectRaw('driver_id, AVG(driver_rating) as rating_avg')
            ->whereNotNull('driver_rating')->groupBy('driver_id')->get();

        foreach ($avgs as $avg) {
            Driver::find($avg['driver_id'])->update(['rating' => $avg['rating_avg']]);
        }
    }
}
