<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabRequestEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cab_request_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('request_id');
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8);
            $table->text('path', 200000);
            $table->double('distance', 15, 2)->default(0); 
            $table->timestamp('created_at')->useCurrent();

            
            $table->foreign('request_id')->references('id')->on('cab_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cab_request_entries');
    }
}
