<?php

namespace App\GraphQL\Mutations;

use \App\User;

class Upload
{
    /**
     * Upload a file, store it on the server and return the path.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function __invoke($root, array $args)
    {
        
        $uploadedFile = \Storage::disk('local')->putFile('uploads', $args['avatar']);

        $path = env('APP_URL') . '/' . $uploadedFile;

        $user = User::find($args['id']);
        $user->update(['avatar' => $path]);

        return $user;
    }
}