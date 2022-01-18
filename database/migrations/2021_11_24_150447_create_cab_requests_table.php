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
            $table->enum('status', [
                'SCHEDULED',
                'SEARCHING',
                'SENDING',
                'ACCEPTED', 
                'ARRIVED',
                'STARTED',
                'COMPLETED',
                'CANCELLED',
            ]);
            $table->json('history')->nullable();
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

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
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
