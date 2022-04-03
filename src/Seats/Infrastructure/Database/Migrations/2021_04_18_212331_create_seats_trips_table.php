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
            $table->string('name_ar')->nullable();
            $table->uuid('log_id')->nullable();
            $table->dateTime('ready_at')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->unsignedBigInteger('line_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->date('start_date');
            $table->json('days');
            $table->date('end_date');
            $table->float('base_price', 8, 2)->default(0.00);
            $table->float('distance_price', 8, 2)->default(0.00);
            $table->integer('minimum_distance')->default(0);
            $table->boolean('bookable')->default(false);
            $table->boolean('ac')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('line_id');
            $table->index('log_id');
            $table->index('partner_id');
            $table->index('driver_id');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
            
            $table->foreign('line_id')->references('id')->on('seats_lines')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
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
