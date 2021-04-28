<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('emergency_no')->nullable();
            $table->string('secondary_no')->nullable();
            $table->string('title')->nullable();
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->enum('payment_method', ['CASH', 'CARD', 'FAWRY'])->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude',15,8)->nullable();
            $table->float('wallet_balance')->default(0);
            $table->decimal('rating', 4, 2)->default(5);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('ref_code')->nullable();
            $table->string('device_id')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['provider', 'provider_id']);
            $table->index('created_at');
            
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
