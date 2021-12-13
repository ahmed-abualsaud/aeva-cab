<?php

namespace App\Http\Controllers\Mutations;

use App\Repository\Eloquent\Mutations\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController 
{
    private $adminRepository;

    public function  __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'emailOrPhone' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->adminRepository->login($request->all());
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

}