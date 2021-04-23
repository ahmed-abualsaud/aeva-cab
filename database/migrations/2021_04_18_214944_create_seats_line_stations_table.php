<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsLineStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_line_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 
            $table->unsignedBigInteger('line_id');
            $table->integer('duration')->default(0);
            $table->integer('distance')->default(0);
            $table->enum('state', ['START','PICKABLE','END'])->default('PICKABLE');
            $table->smallInteger('order')->nullable();
            $table->timestamps();

            $table->index('line_id');
            
            $table->foreign('line_id')->references('id')->on('seats_lines')->onDelete('cascade');
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
