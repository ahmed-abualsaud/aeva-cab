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
            $table->string('name')->unique();
            $table->boolean('roles')->default(0);
            $table->boolean('stats')->default(0);
            $table->boolean('users')->default(0);
            $table->boolean('fleets')->default(0);
            $table->boolean('promocodes')->default(0);
            $table->boolean('business')->default(0);
            $table->boolean('commute')->default(0);
            $table->boolean('ondemand')->default(0);
            $table->boolean('cab')->default(0);
            $table->timestamps();
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
