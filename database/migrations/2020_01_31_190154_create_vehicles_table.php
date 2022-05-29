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
            $table->string('license_plate')->unique()->nullable();
            $table->unsignedBigInteger('car_type_id')->nullable();
            $table->unsignedBigInteger('car_model_id')->nullable();
            $table->unsignedBigInteger('car_make_id')->nullable();
            $table->date('license_expires_on')->nullable();
            $table->string('color')->nullable();
            $table->year('year')->nullable();
            $table->unsignedSmallInteger('seats')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->unsignedBigInteger('device_id')->nullable();
            $table->string('code')->nullable();
            $table->string('text')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('partner_id');
            $table->index('terminal_id');
            
            $table->foreign('car_make_id')->references('id')->on('car_makes')->onDelete('set null');
            $table->foreign('car_type_id')->references('id')->on('car_types')->onDelete('set null');
            $table->foreign('car_model_id')->references('id')->on('car_models')->onDelete('set null');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('set null');
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
