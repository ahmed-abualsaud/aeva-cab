<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePartnerTripIdStationsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trip_stations', function (Blueprint $table) {
            $table->dropForeign('partner_trip_stations_partner_trip_id_foreign');
            $table->renameColumn('partner_trip_id', 'trip_id');
            $table->foreign('trip_id')->references('id')->on('partner_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_trip_stations', function (Blueprint $table) {
            $table->dropForeign('partner_trip_stations_trip_id_foreign');
            $table->renameColumn('trip_id', 'partner_trip_id');
            $table->foreign('partner_trip_id')->references('id')->on('partner_trips')->onDelete('cascade');
        });
    }
}
