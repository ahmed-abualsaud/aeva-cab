<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMakeToModelsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_models', function (Blueprint $table) {
            $table->dropUnique('car_models_name_unique');
        });

        Schema::table('car_models', function (Blueprint $table) {
            $table->unsignedBigInteger('make_id');

            $table->unique(['name', 'make_id'], 'car_make_model');

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
        Schema::table('car_models', function (Blueprint $table) {
            $table->dropColumn('make_id');
            $table->dropUnique('car_make_model');
            $table->unique('name');
        });
    }
}
