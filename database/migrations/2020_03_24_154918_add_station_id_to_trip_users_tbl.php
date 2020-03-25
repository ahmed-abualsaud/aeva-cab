<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStationIdToTripUsersTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trip_users', function (Blueprint $table) {
            $table->renameColumn('partner_trip_id', 'trip_id');
            $table->renameColumn('partner_user_id', 'user_id');
            $table->unsignedBigInteger('station_id')->nullable()->after('partner_user_id');

            $table->foreign('station_id')->references('id')->on('partner_trip_stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_trip_users', function (Blueprint $table) {
            $table->renameColumn('trip_id', 'partner_trip_id');
            $table->renameColumn('user_id', 'partner_user_id');
            $table->dropColumn('station_id');
            $table->dropForeign('partner_trip_users_station_id_foreign');
        });
    }
}
