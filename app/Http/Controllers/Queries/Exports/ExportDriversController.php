<?php

namespace App\Http\Controllers\Queries\Exports;

use App\Driver;
use App\Exports\DriversExport;
use App\Http\Controllers\Controller;
use App\Traits\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportDriversController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new DriversExport(Driver::searchApplied()))->download();
    }
}
