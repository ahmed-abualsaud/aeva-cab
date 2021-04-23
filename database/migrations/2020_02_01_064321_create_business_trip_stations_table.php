<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_stations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8); 
            $table->unsignedBigInteger('trip_id');
            $table->integer('duration')->default(0);
            $table->integer('distance')->default(0);
            $table->timestamp('accepted_at')->nullable();
            $table->string('request_type')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->enum('state', ['START','END','PICKABLE','PENDING','DESTINATION'])->default('PENDING');
            $table->smallInteger('order')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('trip_id');
            
            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_stations');
    }
}
