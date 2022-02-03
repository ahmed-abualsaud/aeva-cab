<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabTripEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_trip_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id');
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 

            $table->index('request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cab_trip_entries');
    }
}
