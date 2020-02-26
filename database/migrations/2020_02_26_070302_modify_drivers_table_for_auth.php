<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDriversTableForAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'car_no', 'status']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->string('password')->nullable();
            $table->boolean('status')->default(1);
            $table->string('email')->nullable()->change();
            $table->string('phone')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('last_name');
            $table->string('car_no')->nullable(); 
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->renameColumn('name', 'first_name');
            $table->string('email')->unique()->change();
            $table->string('phone')->nullable()->change();
        });
    }
}
