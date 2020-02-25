<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPartnerTripUserUniqueness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trip_users', function (Blueprint $table) {
            $table->unique(['partner_trip_id', 'partner_user_id'], 'partner_trip_user');
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
            $table->dropUnique('partner_trip_user');
        });
    }
}
