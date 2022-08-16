<?php

namespace App\Console\Commands;

use App\Jobs\ChangeDriverStatus as ChangeDriverStatusJob;
use Illuminate\Console\Command;

class ChangeDriverStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:driver-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change driver status from online to offline acc to settings';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        ChangeDriverStatusJob::dispatch();
    }
}
