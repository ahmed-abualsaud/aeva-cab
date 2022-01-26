<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trx_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->nullableMorphs('admin');
            $table->string('source')->nullable();
            $table->float('amount', 8, 2);
            $table->enum('type', ['WALLET_DEPOSIT','WALLET_WITHDRAW','INSURANCE_DEPOSIT','INSURANCE_WITHDRAW']);
            $table->enum('service', ['RENT','TOSCHOOL','TOWORK','PLAYGROUND','SEATS','OTHER']);
            $table->string('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index('partner_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('service');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_transactions');
    }
}
