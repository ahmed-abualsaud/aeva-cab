<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('role_type_id');
            $table->string('employee_id')->nullable();
            $table->boolean('dashboard')->default(0);
            $table->boolean('roles')->default(0);
            $table->boolean('archive')->default(0);
            $table->boolean('communication')->default(0);
            $table->boolean('promocodes')->default(0);
            $table->boolean('business')->default(0);
            $table->boolean('commute')->default(0);
            $table->boolean('ondemand')->default(0);
            $table->boolean('cab')->default(0);
            $table->boolean('fleet')->default(0);
            $table->boolean('payment')->default(0); 
            $table->boolean('cancellation')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('role_type_id')->references('id')->on('role_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
