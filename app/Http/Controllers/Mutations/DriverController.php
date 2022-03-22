<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Mutations\DriverRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverController 
{

    private $driverRepository;

    public function  __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function handleAvatar(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->driverRepository->handleAvatar($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Avatar handled successfully',
            'data' => $data
        ];

        return $response;
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required'],
            'phone' => ['sometimes', Rule::unique('drivers', 'phone')->ignore($request->id, 'id')],
            'email' => ['sometimes', Rule::unique('drivers', 'email')->ignore($request->id, 'id')]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->driverRepository->update($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $data
        ];

        return $response;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'emailOrPhone' => ['required'],
            'password' => ['required'],
            'platform' => [Rule::in(['android', 'ios'])]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->driverRepository->login($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 401);
        }

        $response = [
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => $data
        ];

        return $response;
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required'],
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed']
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->driverRepository->updatePassword($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => $data
        ];

        return $response;
    }
}