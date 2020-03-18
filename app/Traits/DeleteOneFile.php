<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;

trait DeleteOneFile
{
    protected function deleteOneFile($file, $folder)
    {
        try {
            $fileName = explode($folder.'/', $file)[1];
            $exists = Storage::disk('azure')->exists($folder.'/'.$fileName);
            if ($exists) Storage::disk('azure')->delete($folder.'/'.$fileName);
        } catch(\Exception $e) {
            // Do nothing. Simply, file does not exist.
        }
    }
}