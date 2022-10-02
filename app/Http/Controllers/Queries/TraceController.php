<?php

namespace App\Http\Controllers\Queries;

use Aeva\Cab\Domain\Models\Trace;

class TraceController
{

    public function index()
    {
        $traces = Trace::searchApplied()->with('cabRequest')->paginate(50);
        return dashboard_info('traces',compact('traces'));
    }
}
