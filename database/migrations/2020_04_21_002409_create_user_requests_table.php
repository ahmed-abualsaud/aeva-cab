<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('current_driver_id')->nullable();
            $table->unsignedBigInteger('car_type_id');
            $table->enum('status', [
                'SEARCHING',
                'CANCELLED',
                'ACCEPTED', 
                'STARTED',
                'ARRIVED',
                'PICKEDUP',
                'DROPPED',
                'COMPLETED',
                'SCHEDULED',
            ]);
            $table->enum('cancelled_by', ['USER','DRIVER'])->nullable();
            $table->string('cancel_reason')->nullable();
            $table->enum('payment_mode', [
                'CASH',
                'CARD',
                'PAYPAL'
            ]);
            $table->boolean('paid')->default(0);
            $table->boolean('is_track')->default(0);
            $table->double('distance', 15, 8);
            $table->string('travel_time')->nullable();
            $table->string('s_address')->nullable();
            $table->double('s_latitude', 15, 8);
            $table->double('s_longitude', 15, 8);
            $table->string('d_address')->nullable();
            $table->double('d_latitude', 15, 8);
            $table->double('d_longitude', 15, 8);
            $table->double('track_distance', 15, 8)->default(0);
            $table->double('track_latitude', 15, 8)->default(0);
            $table->double('track_longitude', 15, 8)->default(0);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('schedule_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->boolean('user_rated')->default(0);
            $table->boolean('driver_rated')->default(0);
            $table->boolean('use_wallet')->default(0);
            $table->boolean('surge')->default(0);
            $table->longText('route_key')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('current_driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('car_type_id')->references('id')->on('car_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_requests');
    }
}
