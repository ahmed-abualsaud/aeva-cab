<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->uuid('log_id')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->string('subscription_code')->nullable();
            $table->date('start_date');
            $table->json('days');
            $table->time('return_time')->nullable();
            $table->date('end_date');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->boolean('group_chat')->default(false);
            $table->text('route')->nullable();
            $table->enum('type', ['TOSCHOOL','TOWORK','PLAYGROUND']);
            $table->float('price', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('log_id');
            $table->index('partner_id');
            $table->index('driver_id');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
            
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trips');
    }
}
