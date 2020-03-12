<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_id');
            $table->unsignedBigInteger('trip_id');
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('status', [
                'STARTED', 
                'MOVING', 
                'NEAR_YOU', 
                'PICKED_UP', 
                'NOT_PICKED_UP', 
                'DROPPED_OFF', 
                'ARRIVED'
            ])->default('MOVING');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();

            $table->foreign('trip_id')->references('id')->on('partner_trips')->onDelete('cascade');
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
        Schema::dropIfExists('trip_logs');
    }
}
