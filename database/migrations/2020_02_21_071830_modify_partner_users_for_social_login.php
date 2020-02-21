<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPartnerUsersForSocialLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_users', function (Blueprint $table) {
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->renameColumn('first_name', 'name');
            $table->unsignedBigInteger('partner_id')->nullable()->change();
            $table->dropColumn('last_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_users', function (Blueprint $table) {
            $table->dropColumn(['phone_verified_at', 'password', 'provider', 'provider_id']);
            $table->renameColumn('name', 'first_name');
            $table->unsignedBigInteger('partner_id')->nullable(false)->change();
            $table->string('last_name');
        });
    }
}
