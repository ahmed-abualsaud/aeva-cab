<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDestinationLatLngToTripsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trips', function (Blueprint $table) {
            $table->double('d_latitude', 15, 8)->nullable()->after('subscription_code');
            $table->double('d_longitude', 15, 8)->nullable()->after('d_latitude');
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
            $table->dropColumn(['d_latitude', 'd_longitude']);
        });
    }
}
