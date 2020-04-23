<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRequestPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_request_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('promo_code_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_mode')->nullable();
            $table->float('fixed', 10, 2)->default(0);
            $table->float('distance', 10, 2)->default(0);
            $table->float('commision', 10, 2)->default(0);
            $table->float('discount', 10, 2)->default(0);
            $table->float('tax', 10, 2)->default(0);
            $table->float('wallet', 10, 2)->default(0);
            $table->float('surge', 10, 2)->default(0);
            $table->float('total', 10, 2)->default(0);
            $table->float('payable', 8, 2)->default(0);
            $table->float('driver_commission', 8, 2)->default(0);
            $table->float('driver_pay', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('user_requests')->onDelete('cascade');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_request_payments');
    }
}
