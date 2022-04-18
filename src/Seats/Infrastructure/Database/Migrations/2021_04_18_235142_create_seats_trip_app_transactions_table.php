<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripAppTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_app_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trx_id')->nullable();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('trip_id');
            $table->dateTime('trip_time');
            $table->unsignedBigInteger('user_id');
            $table->float('amount', 8, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'FAWRY'])->default('CASH');
            $table->string('notes')->nullable();
            $table->enum('created_by', ['USER', 'DRIVER'])->default('DRIVER');
            $table->timestamps();
            $table->softDeletes();

            $table->index('booking_id');
            $table->index(['trip_id', 'trip_time']);
            $table->index('user_id');
            $table->index('created_at');

            $table->foreign('booking_id')
                ->references('id')
                ->on('seats_trip_bookings')
                ->onDelete('cascade');

            $table->foreign('trip_id')
                ->references('id')
                ->on('seats_trips')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_trip_app_transactions');
    }
}
