<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPartnerTripsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trips', function (Blueprint $table) {
            $table->renameColumn('time', 'return_time');
            $table->renameColumn('startDate', 'start_date');
            $table->renameColumn('endDate', 'end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_trips', function (Blueprint $table) {
            $table->renameColumn('return_time', 'time');
            $table->renameColumn('start_date', 'startDate');
            $table->renameColumn('end_date', 'endDate');
        });
    }
}
