<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\CancellationReason;
use App\Repository\Eloquent\Controllers\CancellationReasonRepository;

class CancellationReasonController extends Controller
{
    private $cancellationReasonRepository;

    public function __construct(CancellationReasonRepository $cancellationReasonRepository)
    {
        $this->cancellationReasonRepository = $cancellationReasonRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return $this->cancellationReasonRepository->index();
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
            'category_id' => ['required', 'exists:cancellation_reason_categories,id'],
            'reason' => ['required']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonRepository->store($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['exists:cancellation_reasons']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonRepository->show($id);
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
            'id' => ['exists:cancellation_reasons'],
            'category_id' => ['exists:cancellation_reason_categories,id']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonRepository->update($request->all(), $id);
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
            'id' => ['exists:cancellation_reasons']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        return $this->cancellationReasonRepository->destroy($id);
    }
}
