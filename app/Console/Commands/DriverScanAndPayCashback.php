<?php

namespace App\Console\Commands;
use App\Jobs\DriverScanAndPayCashback as CashbackJob;
use Illuminate\Console\Command;

class DriverScanAndPayCashback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan-and-pay:driver-cashback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'drivers scan and pay cashback acc to settings';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        CashbackJob::dispatch();
    }
}
