<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('make_id');
            $table->unsignedSmallInteger('seats')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_public')->default(1);
            $table->smallInteger('order')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'make_id'], 'car_make_model');

            $table->foreign('type_id')->references('id')->on('car_types')->onDelete('cascade');
            $table->foreign('make_id')->references('id')->on('car_makes')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_models');
    }
}
