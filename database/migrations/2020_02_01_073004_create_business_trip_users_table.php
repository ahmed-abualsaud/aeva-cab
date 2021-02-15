<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('station_id')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->timestamp('subscription_verified_at')->nullable();
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_picked_up')->default(false);
            $table->string('creator_type')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();

            $table->unique(['trip_id', 'user_id']);
            $table->index('user_id');
            $table->index('station_id');
            $table->index('destination_id');

            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('business_trip_stations')->onDelete('set null');
            $table->foreign('destination_id')->references('id')->on('business_trip_stations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_users');
    }
}
