<?php

namespace App\GraphQL\Mutations;

use \App\User;
use \App\Driver;
use \App\Partner;
use \App\Fleet;
use \App\Document;
use Illuminate\Support\Facades\Storage;

class Upload
{

    protected $storageDisk = 'local';
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

        if ($user->avatar) $this->deleteFile($user->avatar);
        $path = $this->uploadFile($args['avatar']);
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

        if ($fleet->avatar) $this->deleteFile($fleet->avatar);
        $path = $this->uploadFile($args['avatar']);
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

        if ($driver->avatar) $this->deleteFile($driver->avatar);
        $path = $this->uploadFile($args['avatar']);
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

        if ($partner->logo) $this->deleteFile($partner->logo);
        $path = $this->uploadFile($args['logo']);
        $partner->update(['logo' => $path]);

        return $partner;
    }

    public function document($root, array $args)
    {
        $file = $args['file'];
        $url = $this->uploadFile($file);
        $input = collect($args)->except(['file', 'directive'])->toArray();
        $input['url'] = $url;
        
        if (!array_key_exists('name', $args)) {
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

        $this->deleteFile($document->url);
        $document->delete();

        return "Document has been deleted.";
    }

    protected function uploadFile($file)
    {
        try {
            $fileHash = str_replace('.' . $file->extension(), '', $file->hashName());
            $fileName = $fileHash . '.' . $file->getClientOriginalExtension();
            $uploadedFile = Storage::disk($this->storageDisk)->putFileAs('uploads', $file, $fileName);
            return env('APP_URL') . '/' . $uploadedFile;
        } catch(\Exception $e) {
            throw new \Exception('We could not able to upload this file. ' . $e->getMessage());
        }
    }

    protected function deleteFile($file)
    {
        try {
            $fileName = explode('uploads/', $file)[1];
            $exists = Storage::disk($this->storageDisk)->exists('uploads/'.$fileName);
            if ($exists) Storage::disk($this->storageDisk)->delete('uploads/'.$fileName);
        } catch(\Exception $e) {
            // file does not exist.
        }
    }
}