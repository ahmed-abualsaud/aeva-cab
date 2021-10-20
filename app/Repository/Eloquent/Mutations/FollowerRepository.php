<?php

namespace App\Repository\Eloquent\Mutations;

use App\Follower;
use App\Repository\Eloquent\BaseRepository;
use App\Exceptions\CustomException;

class FollowerRepository extends BaseRepository
{
    public function __construct(Follower $model)
    {
        parent::__construct($model);
    }


    public function create(array $args)
    {
        $input = collect($args)->except(['directive'])->toArray();

        $followers =  $this->model->where('user_id', $input['user_id'])
            ->where('follower_id', $input['follower_id'])
            ->where('trip_id', $input['trip_id'])->get();
        
        if(count($followers) > 0)
            throw new CustomException(__('lang.the_following_already_exists'));

        return $this->model->create($input);
    }

    public function destroy(array $args)
    {
        return $this->model->where('id', $args['id'])->delete();
    }
}
