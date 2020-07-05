<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerTripStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_trip_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 
            $table->unsignedBigInteger('trip_id');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('state')->default('PENDING');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trip_id')->references('id')->on('partner_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_trip_stations');
    }
}
