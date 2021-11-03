<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_absent');
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'date']);
            $table->index('user_id');

            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_attendance');
    }
}
