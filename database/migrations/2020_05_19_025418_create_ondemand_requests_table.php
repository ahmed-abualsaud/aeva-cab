<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOndemandRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ondemand_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('verb', ['RENT', 'ORGANIZE', 'SUBSCRIBE']);
            $table->enum('type', ['RENT', 'EVENT', 'TRIP', 'RIDESHARE', 'CARSHARE'])->nullable();
            $table->string('event_name')->nullable();
            $table->enum('frequency', ['DAILY', 'ONE_TIME'])->nullable();
            $table->enum('way', ['ONE_WAY', 'TWO_WAYS'])->nullable();
            $table->enum('classification', ['EDUCATIONAL', 'CORPORATE', 'INDIVIDUAL', 'GROUP'])->nullable();
            $table->boolean('find_people')->default(0);
            $table->string('contact_phone')->nullable();
            $table->unsignedInteger('no_of_users')->nullable();
            $table->double('from_lat', 15, 8);
            $table->double('from_lng', 15, 8);
            $table->double('to_lat', 15, 8);
            $table->double('to_lng', 15, 8);
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date'); 
            $table->enum('status', [
                'ACCEPTED', 
                'REJECTED',
                'STARTED',
                'COMPLETED',
                'CANCELLED',
                'PENDING'
            ])->default('PENDING');
            $table->string('comment')->nullable();
            $table->string('response')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ondemand_requests');
    }
}
