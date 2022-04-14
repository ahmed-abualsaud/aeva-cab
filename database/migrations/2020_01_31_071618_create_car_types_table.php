<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('photo')->nullable();
            $table->unsignedSmallInteger('seats');
            $table->float('base_fare', 8, 3);
            $table->float('distance_price', 8, 3);
            $table->float('duration_price', 8, 3);
            $table->float('surge_factor', 3, 2);
            $table->unsignedSmallInteger('min_distance');
            $table->boolean('is_public')->default(1);
            $table->smallInteger('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_types');
    }
}
