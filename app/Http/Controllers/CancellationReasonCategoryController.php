<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\CancellationReasonCategory;
use App\Repository\Eloquent\Controllers\CancellationReasonCategoryRepository;

class CancellationReasonCategoryController extends Controller
{
    private $cancellationReasonCategoryRepository;

    public function __construct(CancellationReasonCategoryRepository $cancellationReasonCategoryRepository)
    {
        $this->cancellationReasonCategoryRepository = $cancellationReasonCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return $this->cancellationReasonCategoryRepository->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Request  $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'category' => ['required', 'unique:cancellation_reason_categories']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonCategoryRepository->store($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Http\Requests\Request  $request
     * @return Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $locale = $request->getPreferredLanguage(['en', 'ar']);
        $input = $request->all();
        $validator = Validator::make($input, [
            'category' => ['exists:cancellation_reason_categories']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonCategoryRepository->show($input['category'], $locale);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $id
     * @param  App\Http\Requests\Request  $request
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation_data = $request->all();
        $validation_data['id'] = $id;
        $validator = Validator::make($validation_data, [
            'id' => ['exists:cancellation_reason_categories'],
            'category' => ['unique:cancellation_reason_categories,category,'.$id]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonCategoryRepository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['exists:cancellation_reason_categories']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonCategoryRepository->destroy($id);
    }
}
