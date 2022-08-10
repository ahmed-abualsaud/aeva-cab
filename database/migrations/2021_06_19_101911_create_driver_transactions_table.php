<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('driver_id');
            $table->unsignedBigInteger('admin_id');
            $table->float('amount', 8, 2);
            $table->enum('type', ['Wallet Deposit','Wallet Withdraw', 'Cashout']);
            $table->string('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('driver_id');
            $table->index('type');

            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_transactions');
    }
}
