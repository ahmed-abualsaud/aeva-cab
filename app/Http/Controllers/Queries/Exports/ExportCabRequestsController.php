<?php

namespace App\Http\Controllers\Queries\Exports;

use Aeva\Cab\Domain\Models\CabRequest;
use App\Exports\CabRequestsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportCabRequestsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new CabRequestsExport(CabRequest::query()))->download();
    }
}
