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
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code');
            $table->integer('duration')->nullable();
            $table->integer('distance')->nullable();
            $table->float('base_price', 8, 2)->default(0.00);
            $table->float('distance_price', 8, 2)->default(0.00);
            $table->integer('minimum_distance')->default(0);
            $table->text('route')->nullable();
            $table->timestamps();

            $table->index('partner_id');
            $table->index('city_id');
            $table->index('created_at');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
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
