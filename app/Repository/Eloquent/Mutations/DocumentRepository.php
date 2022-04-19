<?php

namespace App\Repository\Eloquent\Mutations;

use \App\Document;
use \App\Traits\HandleUpload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repository\Eloquent\BaseRepository;

class DocumentRepository extends BaseRepository
{
    use HandleUpload;

    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    public function create(array $args)
    {
        $file = $args['file'];
        $url = $this->uploadOneFile($file, 'documents');
        $input = collect($args)->except(['file', 'directive'])->toArray();
        $input['url'] = $url;
        
        if (!$input['name']) {
            $input['name'] = $file->getClientOriginalName();
        }

        $document = $this->model->create($input);

        return $document;
    }

    public function update(array $args)
    {
        try {
            $document = $this->model->findOrFail($args['id']);
        } catch (ModelNotFoundException $e) {
            throw new CustomException(__('lang.document_not_found'));
        }

        $input = collect($args)->except(['file', 'directive'])->toArray();

        if (array_key_exists('file', $args) && $args['file'] != null) {
            $file = $args['file'];
            if ($document->url) $this->deleteOneFile($document->url, 'documents');
            $url = $this->uploadOneFile($file, 'documents');
            $input['url'] = $url;
            
            if (array_key_exists('name', $args) && !$input['name']) {
                $input['name'] = $file->getClientOriginalName();
            }
        }

        $document->update($input);

        return $document;
    }

    public function destroy(array $args)
    {
        try {
            $document = $this->model->findOrFail($args['id']);
            $this->deleteOneFile($document->url, 'documents');
            $document->delete();
        } catch(ModelNotFoundException $e) {
            throw new \Exception(__('lang.document_not_found') . $e->getMessage());
        }

        return __('lang.document_deleted');
    }
}
