<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 
            $table->unsignedBigInteger('trip_id');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->enum('state', ['START','PICKABLE','END'])->default('PICKABLE');
            $table->smallInteger('order')->nullable();
            $table->timestamps();

            $table->index('trip_id');
            
            $table->foreign('trip_id')->references('id')->on('seats_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_stations');
    }
}
