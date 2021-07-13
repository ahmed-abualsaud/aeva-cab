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
