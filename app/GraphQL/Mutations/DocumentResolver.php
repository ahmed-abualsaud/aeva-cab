<?php

namespace App\GraphQL\Mutations;

use App\Repository\Eloquent\Mutations\DocumentRepository;

class DocumentResolver
{
    private $documentRepository;

    public function  __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Upload a file, store it on the server and return the model.
     *
     * @param  mixed  $root
     * @param  mixed[]  $args
     * @return string|null
     */
    public function create($root, array $args)
    {
        return $this->documentRepository->create($args);
    }

    public function update($root, array $args)
    {
        return $this->documentRepository->update($args);
    }

    public function delete($root, array $args)
    {
        return $this->documentRepository->destroy($args);
    }
}