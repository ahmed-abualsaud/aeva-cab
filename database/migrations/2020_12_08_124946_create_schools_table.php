<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_zones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('schools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8);
            $table->string('address')->nullable();
            $table->unsignedBigInteger('zone_id');
            $table->timestamps();

            $table->unique(['zone_id', 'name'], 'zone_school_unique');

            $table->foreign('zone_id')->references('id')->on('school_zones')->onDelete('cascade');
        });

        Schema::create('school_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->time('enter_time');
            $table->time('exit_time');
            $table->unsignedBigInteger('school_id');
            $table->timestamps();

            $table->unique(['school_id', 'name'], 'school_grade_unique');

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        Schema::create('school_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('grade_id');
            $table->string('student_name');
            $table->string('contact_phone')->nullable();
            $table->double('pickup_lat', 15, 8);
            $table->double('pickup_lng', 15, 8);
            $table->string('pickup_address');
            $table->enum('package', ['BASIC', 'PREMIUM']);
            $table->string('days');
            $table->enum('status', [
                'ACCEPTED', 
                'REJECTED',
                'CANCELLED',
                'PENDING'
            ])->default('PENDING');
            $table->string('comment')->nullable();
            $table->string('response')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('created_at');
            $table->index(['school_id', 'grade_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('grade_id')->references('id')->on('school_grades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_requests');
        Schema::dropIfExists('school_grades');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('school_zones');
    }
}