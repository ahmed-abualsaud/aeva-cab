<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerTripUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_trip_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('partner_trip_id');
            $table->unsignedBigInteger('partner_user_id');
            $table->timestamp('subscription_verified_at')->nullable();
            $table->date('subscription_expires_on')->nullable();
            $table->timestamps();

            $table->foreign('partner_trip_id')->references('id')->on('partner_trips')->onDelete('cascade');
            $table->foreign('partner_user_id')->references('id')->on('partner_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_trip_users');
    }
}
