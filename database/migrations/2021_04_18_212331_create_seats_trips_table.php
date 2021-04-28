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
            $table->string('name');
            $table->uuid('log_id')->nullable();
            $table->unsignedBigInteger('line_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->date('start_date');
            $table->json('days');
            $table->date('end_date');
            $table->float('price', 8, 2)->nullable();
            $table->boolean('bookable')->default(false);
            $table->timestamps();

            $table->index('line_id');
            $table->index('log_id');
            $table->index('partner_id');
            $table->index('driver_id');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
            
            $table->foreign('line_id')->references('id')->on('seats_lines')->onDelete('cascade');
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
