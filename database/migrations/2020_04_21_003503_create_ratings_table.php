<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() 
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('ratingable_id');
            $table->string('ratingable_type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->integer('user_rating')->default(0);
            $table->integer('driver_rating')->default(0);
            $table->string('user_comment')->nullable();
            $table->string('driver_comment')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('ratings');
    }
}
