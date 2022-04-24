<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('log_id');
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->dateTime('trip_time');
            $table->decimal('rating', 4, 2)->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('log_id');
            $table->index(['user_id', 'trip_id']);
            
            $table->foreign('trip_id')->references('id')->on('seats_trips')->onDelete('set null');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_ratings');
    }
}
