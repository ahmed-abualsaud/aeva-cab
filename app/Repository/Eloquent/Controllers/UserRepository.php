<?php

namespace App\Repository\Eloquent\Controllers;

use App\User;
use App\Traits\HandleUpload;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class UserRepository extends Controller
{
    use HandleUpload;

    private $model;

    public function __construct(User $model)
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
            $user = $this->model->findOrFail($request->id);
        } catch (ModelNotFoundException $e) {
            return response()->json(__('lang.user_not_found'), 500);
        }

        if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
        $url = $this->uploadOneFile($request->avatar, 'avatars');

        $user->update(['avatar' => $url]);

        return response()->json($user);
    }

    public function getLanguage(Request $request) {

        $request->session()->forget('locale');
        return __('auth.failed');
    }
}
