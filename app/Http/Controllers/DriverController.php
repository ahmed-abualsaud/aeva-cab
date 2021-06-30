<?php

namespace App\Http\Controllers;

use App\Driver;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DriverController extends Controller
{
    use HandleUpload;

    public function handleAvatar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $driver = Driver::findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(__('lang.driver_not_found'), 404);
        }

        if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $driver->update(['avatar' => $url]);

        return response()->json($driver);
    }

}
