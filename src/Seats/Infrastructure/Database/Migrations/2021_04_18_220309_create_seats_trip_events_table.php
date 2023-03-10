<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_events', function (Blueprint $table) {
            $table->uuid('log_id');
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->dateTime('trip_time');
            $table->json('content');
            $table->text('map_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('log_id');
            $table->index('trip_id');
            $table->index('created_at');

            $table->foreign('trip_id')->references('id')->on('seats_trips')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_events');
    }
}
