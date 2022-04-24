<?php

namespace App\Console\Commands;

use Carbon\Carbon;

use Illuminate\Http\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use Aeva\Seats\Domain\Models\SeatsTripTerminalTransaction;

class TaskTakeDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take database backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dbName = config('custom.db_name');
        $dbUser = config('custom.db_user');
        $dbPass = config('custom.db_pass');
        $dbPath = config('custom.db_backup_path');


        if (!file_exists($dbPath)) {
            mkdir($dbPath, 0777, true);
        }

        if (!str_ends_with($dbPath, '/')) {
            $dbPath .= '/';
        }

        $fileName = 'backup_'.date('Y-m-dTH:i:s').'.sql';

        $dbPath .= $fileName;

        $executeCmd = 'mysqldump -u'.$dbUser.' -p'.$dbPass.' '.$dbName.' -r '.$dbPath;

        system($executeCmd);

        Storage::disk('s3')->putFileAs('backups', new File($dbPath), $fileName);

        SeatsTripTerminalTransaction::where('created_at', '<', Carbon::now()->subMonth(2))->delete();

        system('rm '.$dbPath);

    }
}
