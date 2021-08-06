<?php

namespace App\Repository\Eloquent\Controllers;

use App\Driver;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class DriverRepository extends Controller
{
    use HandleUpload;
    
    private $model;
    
    public function __construct(Driver $model)
    {
        $this->model = $model;
    }

    public function handleAvatar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $driver = $this->model->findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(__('lang.driver_not_found'), 404);
        }

        if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $driver->update(['avatar' => $url]);

        return response()->json($driver);
    }
    
}
