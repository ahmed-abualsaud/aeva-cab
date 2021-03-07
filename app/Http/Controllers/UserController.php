<?php

namespace App\Http\Controllers;

use App\User;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    use HandleUpload;

    public function handleAvatar(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = User::findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json('The provided user ID is not found.', 500);
        }

        if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $user->update(['avatar' => $url]);

        return response()->json($user);
    }

}
