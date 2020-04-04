<?php

namespace App\GraphQL\Mutations;

use \App\Document;
use \App\Traits\UploadFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DocumentResolver
{
    use UploadFile;

    /**
     * Upload a file, store it on the server and return the model.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function create($root, array $args)
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

    public function delete($root, array $args)
    {
        try {
            $document = Document::findOrFail($args['id']);
        } catch(ModelNotFoundException $e) {
            throw new \Exception('Document with the provided ID is not found. ' . $e->getMessage());
        }

        $this->deleteOneFile($document->url, 'documents');
        $document->delete();

        return "Document has been deleted.";
    }
}