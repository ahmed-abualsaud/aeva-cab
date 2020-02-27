<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDriverVehicleUniqueness extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_vehicles', function (Blueprint $table) {
            $table->unique(['driver_id', 'vehicle_id'], 'driver_vehicle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_vehicles', function (Blueprint $table) {
            $table->dropUnique('driver_vehicle');
        });
    }
}
