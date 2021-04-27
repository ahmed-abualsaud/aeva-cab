<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTripTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_trip_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('log_id')->nullable();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('trip_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->float('paid', 8, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'FAWRY'])->default('CASH');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('log_id');
            $table->index('booking_id');
            $table->index('trip_id');
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
        Schema::dropIfExists('seats_trip_transactions');
    }
}
