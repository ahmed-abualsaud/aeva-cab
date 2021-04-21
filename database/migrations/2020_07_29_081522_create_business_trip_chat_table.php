<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessTripChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_trip_chat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('log_id');
            $table->morphs('sender');
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('message');
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index(['log_id', 'is_private']);
            $table->index(['sender_id', 'recipient_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_trip_chat');
    }
}
