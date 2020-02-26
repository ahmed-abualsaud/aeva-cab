<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFirstNameToNameFleetsTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'status']);
        });

        Schema::table('fleets', function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->boolean('status')->default(1);
            $table->unique('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fleets', function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dropUnique('fleets_phone_unique');
        });
    }
}
