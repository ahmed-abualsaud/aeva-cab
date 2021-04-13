<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->float('amount', 10, 2);
            $table->smallInteger('usage')->default(1);
            $table->date('expires_on');
            $table->enum('type', ['toschool','towork', 'seats', 'ondemand']);
            $table->softDeletes();
            $table->timestamps();

            $table->index('type');
            $table->index('expires_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_codes');
    }
}
