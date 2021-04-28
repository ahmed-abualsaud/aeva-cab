<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats_lines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('partner_id');
            $table->string('name');
            $table->string('code');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->float('price', 8, 2)->nullable();
            $table->text('route')->nullable();
            $table->timestamps();

            $table->index('partner_id');
            $table->index('created_at');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats_lines');
    }
}
