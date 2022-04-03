<?php

namespace Qruz\Seats\Domain\Repository\Eloquent\Mutations;

use Illuminate\Http\Request;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;

use Qruz\Seats\Domain\Models\SeatsTripTerminalTransaction;
use Qruz\Seats\Domain\Exports\SeatsTripTerminalTransactionExport;

class SeatsTripTerminalTransactionRepository extends Controller
{
    private $model;

    public function __construct(SeatsTripTerminalTransaction $model)
    {
        $this->model = $model;
    }

    public function create(Request $req) {

        try {

            $obj = json_decode(json_encode($req->obj));
    
            $data = [
                'trx_id' => $obj->id,
                'partner_id' => $obj->profile_id,
                'terminal_id' => $obj->terminal_id,
                'source' => $this->getSource($obj),
                'amount' => $obj->amount_cents/100,
                'status' => $this->getStatus($obj),
                'created_at' => $obj->created_at,
            ];
    
            return $this->model->create($data);
            
        } catch (\Exception $e) {
            throw new CustomException(__('lang.create_transaction_failed'));
        }
    }

    public function export(Request $req) 
    {
        $filename = preg_replace('/-|:|\s+/', '_', now()).'_transactions.xlsx';
        $partner = $req->query('partner');
        $terminal = $req->query('terminal');
        $period = $req->query('period');
        $searchFor = $req->query('searchFor');
        $searchQuery = $req->query('searchQuery');

        return (new SeatsTripTerminalTransactionExport($partner, $terminal, $period, $searchFor, $searchQuery))
            ->download($filename);
    }
    
    protected function getStatus($obj)
    {
        if(!($obj->pending) && !($obj->success))
    		return 'DECLINED';
    	
        if (!($obj->pending) && ($obj->success))
    		return 'SUCCESS';
    	
        return 'PENDING';
    }

    protected function getSource($obj)
    {
        try {
            return $obj->source_data->type;
        } catch (\Exception $e) {
            return $obj->api_source;
        }
    }
}
