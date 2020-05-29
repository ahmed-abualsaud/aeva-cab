<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabRequestFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_request_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('driver_id');
            $table->integer('status')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('cab_request_filters');
    }
}
