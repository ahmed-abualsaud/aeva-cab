<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('subscription_id');
            $table->boolean('is_scheduled')->default(true);
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_picked_up')->default(false);
            $table->json('days');
            $table->float('payable', 8, 2)->default(0.00);
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'student_id', 'trip_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('business_trip_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_students');
    }
}
