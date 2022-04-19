<?php

namespace App\Http\Controllers\Mutations;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Repository\Eloquent\Mutations\DocumentRepository;

class DocumentController 
{
    private $documentRepository;

    public function  __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    public function uploadDocument(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|numeric',
            'file' => 'required|mimes:jpeg,png,jpg,pdf',
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors()->first(),
            ];
            return response()->json($response, 400);
        }

        try {
            $data = $this->documentRepository->update($request->all());
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }

        $response = [
            'success' => true,
            'message' => 'Document uploaded successfully',
            'data' => $data
        ];

        return $response;
    }
}