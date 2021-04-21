<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('log_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->date('start_date');
            $table->json('days');
            $table->date('end_date');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->float('price', 8, 2)->nullable();
            $table->text('route')->nullable();
            $table->boolean('bookable')->default(false);
            $table->timestamps();

            $table->index('log_id');
            $table->index('partner_id');
            $table->index('driver_id');
            $table->index(['start_date', 'end_date']);
            
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trips');
    }
}
