<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerTripStationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_trip_station_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['station_id', 'user_id']);

            $table->foreign('station_id')->references('id')->on('partner_trip_stations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('partner_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_trip_station_users');
    }
}
