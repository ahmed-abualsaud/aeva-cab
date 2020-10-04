<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOndemandRequestLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ondemand_request_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->double('from_lat', 15, 8);
            $table->double('from_lng', 15, 8);
            $table->double('to_lat', 15, 8);
            $table->double('to_lng', 15, 8);
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();

            $table->index('request_id');

            $table->foreign('request_id')->references('id')->on('ondemand_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ondemand_request_lines');
    }
}
