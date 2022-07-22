<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabRequestTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_request_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->string('merchant_name')->nullable();
            $table->float('costs', 8, 2);
            $table->enum('payment_method', ['Cash', 'Card', 'Wallet', 'Cashout', 'Refund']);
            $table->string('uuid');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');
            $table->foreign('request_id')->references('id')->on('cab_requests')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cab_request_transactions');
    }
}
