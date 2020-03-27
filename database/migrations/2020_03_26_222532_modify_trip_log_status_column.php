<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTripLogStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->string('status')->default('MOVING')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_logs', function (Blueprint $table) {
            $table->enum('status', [
                'STARTED', 
                'MOVING', 
                'NEAR_YOU', 
                'PICKED_UP', 
                'NOT_PICKED_UP', 
                'DROPPED_OFF', 
                'ARRIVED'
            ])->default('MOVING')->change();
        });
    }
}
