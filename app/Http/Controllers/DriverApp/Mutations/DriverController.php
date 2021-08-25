<?php

namespace App\Http\Controllers\DriverApp\Mutations;

use App\Repository\Mutations\DriverRepositoryInterface;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DriverController 
{
    private $driverRepository;

    public function  __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $request
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required'],
            'phone' => ['sometimes', Rule::unique('drivers', 'phone')->ignore($request->id, 'id')],
            'email' => ['sometimes', Rule::unique('drivers', 'email')->ignore($request->id, 'id')]
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->driverRepository->update($request->all());
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'emailOrPhone' => ['required'],
            'password' => ['required'],
            'platform' => [Rule::in(['android', 'ios'])]
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);

        return $this->driverRepository->login($request->all());
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => ['required'],
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed']
        ]);

        if ($validator->fails())
            return response()->json($validator->errors(), 500);
            
        return $this->driverRepository->updatePassword($request->all());
    }
}