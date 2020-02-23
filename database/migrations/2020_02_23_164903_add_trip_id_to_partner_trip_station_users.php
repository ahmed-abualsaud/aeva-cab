<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTripIdToPartnerTripStationUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trip_station_users', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id');

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
        Schema::table('partner_trip_station_users', function (Blueprint $table) {
            $table->dropColumn('trip_id');
        });
    }
}
