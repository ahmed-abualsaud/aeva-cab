<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('phone1')->unique();
            $table->string('phone2')->nullable();
            $table->string('type')->nullable();
            $table->enum('size', ['SMALL','MEDIUM','LARGE','ENTERPRISE'])->nullable();
            $table->date('license_expires_on')->nullable();
            $table->date('insurance_expires_on')->nullable();
            $table->unsignedInteger('max_no_of_trips')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
}
