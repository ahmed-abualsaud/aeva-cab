<?php

namespace App\Console\Commands;

use App\Enums\DriverTransactionTypesEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDriverTransactionTypesEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:driver-transaction-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update driver transaction types enum';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        try {
            $types_enum = array_to_migration_enum(DriverTransactionTypesEnum::ALL);
            DB::statement("ALTER TABLE driver_transactions MODIFY COLUMN type ENUM($types_enum)");
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
        }

    }
}
