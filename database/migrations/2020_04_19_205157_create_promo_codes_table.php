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
            $table->float('discount', 10, 2);
            $table->enum('discount_type', ['PERCENT','AMOUNT'])->default('PERCENT');
            $table->date('expires_on');
            $table->enum('type', ['toschool','towork', 'seats', 'ondemand']);
            $table->softDeletes();
            $table->timestamps();

            $table->index('type');
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
