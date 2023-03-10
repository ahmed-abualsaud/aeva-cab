<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id');
            $table->unsignedDecimal('cash', 12, 2)->default(0.00);
            $table->decimal('wallet', 12, 2)->default(0.00);
            $table->decimal('earnings', 12, 2)->default(0.00);
            $table->decimal('rating', 4, 2)->default(5.00);
            $table->bigInteger('received_cab_requests')->default(0);
            $table->bigInteger('accepted_cab_requests')->default(0);
            $table->bigInteger('cancelled_cab_requests')->default(0);
            $table->bigInteger('missed_cab_requests')->default(0);
            $table->bigInteger('dismissed_cab_requests')->default(0);
            $table->bigInteger('total_working_time')->default(0);
            $table->timestamp('activity_updated_at')->nullable();
            $table->timestamps();


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
        Schema::dropIfExists('driver_stats');
    }
}
