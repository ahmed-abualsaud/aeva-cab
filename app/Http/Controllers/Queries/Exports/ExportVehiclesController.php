<?php

namespace App\Http\Controllers\Queries\Exports;

use App\Exports\VehiclesExport;
use App\Http\Controllers\Controller;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportVehiclesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return (new VehiclesExport(Vehicle::searchApplied()))->download();
    }
}
