<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('license_plate')->unique();
            $table->unsignedBigInteger('car_type_id');
            $table->unsignedBigInteger('car_model_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('car_type_id')->references('id')->on('car_types')->onDelete('cascade');
            $table->foreign('car_model_id')->references('id')->on('car_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicles');
    }
}
