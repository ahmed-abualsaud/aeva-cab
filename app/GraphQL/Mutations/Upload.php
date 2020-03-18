<?php

namespace App\GraphQL\Mutations;

use \App\User;
use \App\Driver;
use \App\Partner;
use \App\Fleet;
use \App\Document;
use \App\Traits\UploadOneFile;
use \App\Traits\DeleteOneFile;
use Illuminate\Support\Facades\Storage;

class Upload
{
    use UploadOneFile;
    use DeleteOneFile;

    /**
     * Upload a file, store it on the server and return the model.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function userAvatar($root, array $args)
    {
        try {
            $user = User::findOrFail($args['id']);
        } catch(\Exception $e) {
            throw new \Exception('User with the provided ID is not found. ' . $e->getMessage());
        }

        if ($user->avatar) $this->deleteOneFile($user->avatar, 'avatars');
        $path = $this->uploadOneFile($args['avatar'], 'avatars');
        $user->update(['avatar' => $path]);

        return $user;
    }

    public function fleetAvatar($root, array $args)
    {
        try {
            $fleet = Fleet::findOrFail($args['id']);
        } catch(\Exception $e) {
            throw new \Exception('Fleet with the provided ID is not found. ' . $e->getMessage());
        }

        if ($fleet->avatar) $this->deleteOneFile($fleet->avatar, 'avatars');
        $path = $this->uploadOneFile($args['avatar'], 'avatars');
        $fleet->update(['avatar' => $path]);

        return $fleet;
    }

    public function driverAvatar($root, array $args)
    {
        try {
            $driver = Driver::findOrFail($args['id']);
        } catch(\Exception $e) {
            throw new \Exception('Driver with the provided ID is not found. ' . $e->getMessage());
        }

        if ($driver->avatar) $this->deleteOneFile($driver->avatar, 'avatars');
        $path = $this->uploadOneFile($args['avatar'], 'avatars');
        $driver->update(['avatar' => $path]);

        return $driver;
    }

    public function partnerLogo($root, array $args)
    {
        try {
            $partner = Partner::findOrFail($args['id']);
        } catch(\Exception $e) {
            throw new \Exception('Partner with the provided ID is not found. ' . $e->getMessage());
        }

        if ($partner->logo) $this->deleteOneFile($partner->logo, 'avatars');
        $path = $this->uploadOneFile($args['logo'], 'avatars');
        $partner->update(['logo' => $path]);

        return $partner;
    }

    public function document($root, array $args)
    {
        $file = $args['file'];
        $url = $this->uploadOneFile($file, 'documents');
        $input = collect($args)->except(['file', 'directive'])->toArray();
        $input['url'] = $url;
        
        if (!$input['name']) {
            $input['name'] = $file->getClientOriginalName();
        }

        $document = Document::create($input);

        return $document;
    }

    public function deleteDocument($root, array $args)
    {
        try {
            $document = Document::findOrFail($args['id']);
        } catch(\Exception $e) {
            throw new \Exception('Document with the provided ID is not found. ' . $e->getMessage());
        }

        $this->deleteOneFile($document->url, 'documents');
        $document->delete();

        return "Document has been deleted.";
    }
}