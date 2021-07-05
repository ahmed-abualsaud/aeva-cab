<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripAppTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_app_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trx_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('trip_id');
            $table->date('due_date');
            $table->float('amount', 8, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'FAWRY', 'MANUAL']);
            $table->string('notes')->nullable();
            $table->enum('type', ['TOSCHOOL','TOWORK','PLAYGROUND']);
            $table->timestamps();

            $table->index('user_id');
            $table->index('trip_id');
            $table->index('created_at');
            $table->index('type');

            $table->foreign('subscription_id')->references('id')->on('business_trip_users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('trip_id')->references('id')->on('business_trips')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_transactions');
    }
}
