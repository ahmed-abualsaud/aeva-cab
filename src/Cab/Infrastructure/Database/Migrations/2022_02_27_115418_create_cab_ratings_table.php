<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('request_id');
            $table->dateTime('trip_time');
            $table->decimal('rating', 4, 2)->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();

            $table->index('request_id');
            $table->index(['user_id', 'request_id']);
            
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('cab_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cab_ratings');
    }
}
