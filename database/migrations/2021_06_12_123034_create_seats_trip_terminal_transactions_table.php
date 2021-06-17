<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripTerminalTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_terminal_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trnx_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->string('source');
            $table->float('amount');
            $table->enum('status', ['SUCCESS', 'PENDING', 'DECLINED']);
            $table->dateTime('created_at');


            $table->index('partner_id');
            $table->index('terminal_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_terminal_transactions');
    }
}
