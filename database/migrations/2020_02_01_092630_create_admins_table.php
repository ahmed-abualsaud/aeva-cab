<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('phone')->unique();
            $table->string('avatar')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->boolean('is_super_admin')->default(false);
            $table->json('can');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
