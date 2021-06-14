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
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->string('api_source');
            $table->float('amount');
            $table->string('currency');
            $table->string('type');
            $table->string('sub_type');
            $table->string('status');
            $table->string('created_at');
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
