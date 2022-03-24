<?php

namespace App\Http\Controllers\Mutations;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Repository\Eloquent\Mutations\UserRepository;

class UserController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handleAvatar(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required|numeric'],
            'avatar' => ['required|image|mimes:jpeg,png,jpg|max:2048'],
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->userRepository->handleAvatar($request->all());
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

    public function getLanguage(Request $request) 
    {
        try {
            $data = $this->userRepository->getLanguage($request);
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Language obtained successfully',
            'data' => $data
        ];

        return $response;
    }

    public function create(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'email' => ['unique:users,email'],
            'phone' => ['unique:users,phone'],
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
            $data = $this->userRepository->create($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'User created successfully',
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
            $data = $this->userRepository->login($request->all());
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

    public function socialLogin(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'token' => ['required'],
            'platform' => [Rule::in(['android', 'ios'])],
            'provider' => ['required', Rule::in(['facebook', 'google', 'apple'])]
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->userRepository->socialLogin($request->all());
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
