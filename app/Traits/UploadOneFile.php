<?php

namespace App\Traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadOneFile
{
    public function uploadOneFile(UploadedFile $file, $folder)
    {
        try {
            $fileHash = str_replace('.' . $file->extension(), '', $file->hashName());
            $fileName = $fileHash . '.' . $file->getClientOriginalExtension();
            $uploadedFile = Storage::disk('azure')->putFileAs($folder, $file, $fileName);
            $url = env('AZURE_STORAGE_URL') . '/' . $uploadedFile;
        } catch(\Exception $e) {
            throw new \Exception('We could not able to upload this file. ' . $e->getMessage());
        }

        return $url;
    }
}