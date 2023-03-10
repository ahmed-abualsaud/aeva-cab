<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trip_id')->nullable();
            $table->dateTime('trip_time');
            $table->unsignedBigInteger('pickup_id')->nullable();
            $table->dateTime('pickup_time');
            $table->unsignedBigInteger('dropoff_id')->nullable();
            $table->dateTime('dropoff_time');
            $table->unsignedSmallInteger('seats');
            $table->unsignedBigInteger('promo_code_id')->nullable();
            $table->enum('payment_method', ['Cash', 'Card', 'Fawry'])->default('Cash');
            $table->float('payable', 8, 2)->default(0);
            $table->float('paid', 8, 2)->default(0);
            $table->enum('status', ['Confirmed','Cancelled','Missed','Completed'])
                ->default('Confirmed');
            $table->string('comment')->nullable();
            $table->string('response')->nullable();
            $table->boolean('is_picked_up')->default(false);
            $table->smallInteger('boarding_pass');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'pickup_time']);
            $table->index(['trip_id', 'trip_time']);
            $table->index('pickup_id');
            $table->index('dropoff_id');
            $table->index('promo_code_id');
            $table->index('created_at');
            $table->index('status');

            $table->foreign('trip_id')->references('id')->on('seats_trips')->onDelete('set null');
            $table->foreign('pickup_id')->references('id')->on('seats_line_stations')->onDelete('set null');
            $table->foreign('dropoff_id')->references('id')->on('seats_line_stations')->onDelete('set null');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_bookings');
    }
}
