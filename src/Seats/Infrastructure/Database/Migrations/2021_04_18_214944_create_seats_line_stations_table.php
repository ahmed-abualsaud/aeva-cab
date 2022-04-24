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
            $table->string('name_ar')->nullable();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 
            $table->unsignedBigInteger('line_id')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('distance')->default(0);
            $table->enum('state', ['Start','Pickable','End'])->default('Pickable');
            $table->smallInteger('order')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('line_id');
            
            $table->foreign('line_id')->references('id')->on('seats_lines')->onDelete('set null');
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
