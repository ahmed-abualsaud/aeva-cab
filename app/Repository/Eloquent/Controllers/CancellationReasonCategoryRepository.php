<?php

namespace App\Repository\Eloquent\Controllers;

use Illuminate\Support\Arr;
use App\CancellationReason;
use App\CancellationReasonCategory;
use App\Exceptions\CustomException;

class CancellationReasonCategoryRepository
{
    /**
     * @var CancellationReasonCategory
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(CancellationReasonCategory $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->model->with('reasons')->get();
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array $input
     * @return Illuminate\Http\Response
     */
    public function store(array $input)
    {
        $args = $input;
        $input = Arr::except($input, ['reasons']);
        $data = $this->model->create($input);

        if (array_key_exists('reasons', $args) && $args['reasons']) {
            $now = date('Y-m-d H:i:s');
            foreach ($args['reasons'] as $key => $reason) {
                $args['reasons'][$key] = [
                    'category_id' => $data->id,
                    'reason' => $reason['reason'],
                    'reason_ar' => $reason['reason_ar'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            CancellationReason::insert($args['reasons']);
        }

        $data = $data->with('reasons')->find($data->id);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return Illuminate\Http\Response
     */
    public function show($category, $locale)
    {
        if ($locale == 'ar') {
            $query = $this->model->with('reasons:id,category_id,reason_ar');
        } else {
            $query = $this->model->with('reasons:id,category_id,reason');
        }

        $data = $query->where('category', $category)->get();
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $id
     * @param  array $input
     * @return Illuminate\Http\Response
     */
    public function update(array $input, $id)
    {
        $data = $this->model->with('reasons')->find($id);
        $data->update($input);
        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->model->find($id);
        $data->delete();
        return [
            'success' => true,
            'data' => $data
        ];
    }
}
