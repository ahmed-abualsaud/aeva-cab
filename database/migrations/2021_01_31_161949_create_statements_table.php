<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', [
                'SCHOOL_REQUEST',
                'ONDEMAND_REQUEST',
                'TRIP_SUBSCRIPTION',
                'NEW_USER',
                'NEW_DRIVER',
                'NEW_PARTNER',
                'NEW_ADMIN'
            ]);
            $table->string('title');
            $table->string('title_ar');
            $table->text('body');
            $table->text('body_ar');
            $table->timestamps();
            
            $table->unique('type');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statements');
    }
}
