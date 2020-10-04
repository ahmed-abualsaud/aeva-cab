<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_id');
            $table->time('saturday')->nullable();
            $table->time('sunday')->nullable();
            $table->time('monday')->nullable();
            $table->time('tuesday')->nullable();
            $table->time('wednesday')->nullable();
            $table->time('thursday')->nullable();
            $table->time('friday')->nullable();
            $table->timestamps();

            $table->index('trip_id');

            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_schedules');
    }
}
