<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class addTotalWorkingHoursToDriverLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_logs', function (Blueprint $table) {
            $table->unsignedDecimal('total_working_hours',12,2)->after('total_working_time')->storedAs('total_working_time/60');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_logs', function (Blueprint $table) {
            $table->dropColumn('total_working_hours');
        });
    }
}
