<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('promo_code_id')->nullable();
            $table->enum('status', [
                'Scheduled',
                'Searching',
                'Sending',
                'Accepted', 
                'Arrived',
                'Started',
                'Ended',
                'Completed',
                'Cancelled',
            ]);
            $table->json('history')->nullable();
            $table->text('route_key');
            $table->text('map_url')->nullable();
            $table->dateTime('schedule_time')->nullable();
            $table->dateTime('next_free_time')->nullable();
            $table->boolean('paid')->default(0);
            $table->double('costs', 8, 3)->nullable();
            $table->string('s_address')->nullable();
            $table->double('s_lat', 15, 8);
            $table->double('s_lng', 15, 8);
            $table->string('d_address')->nullable();
            $table->double('d_lat', 15, 8);
            $table->double('d_lng', 15, 8);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
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
        Schema::dropIfExists('cab_requests');
    }
}
