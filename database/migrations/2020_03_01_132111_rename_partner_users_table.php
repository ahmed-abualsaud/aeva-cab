<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePartnerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_users', function (Blueprint $table) {
            $table->dropForeign('partner_users_partner_id_foreign');
            $table->dropUnique('partner_users_email_unique');
        });

        Schema::rename('partner_users', 'users');

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->unique('email');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_partner_id_foreign');
            $table->dropUnique('users_email_unique');
            $table->dropUnique('users_phone_unique');
        });

        Schema::rename('users', 'partner_users');

        Schema::table('partner_users', function (Blueprint $table) {
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->unique('email');
        });
    }
}
