<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workplaces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('latitude', 15, 8);
            $table->double('longitude', 15, 8);
            $table->string('address')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->timestamps();

            $table->index('zone_id');

            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
        });

        Schema::create('work_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('workplace_id');
            $table->unsignedBigInteger('price_package_id');
            $table->string('contact_phone')->nullable();
            $table->double('pickup_lat', 15, 8);
            $table->double('pickup_lng', 15, 8);
            $table->string('pickup_address');
            $table->string('days');
            $table->time('enter_time');
            $table->time('exit_time');
            $table->enum('status', [
                'ACCEPTED', 
                'REJECTED',
                'CANCELLED',
                'PENDING',
                'WAITING',
                'APPROVED'
            ])->default('PENDING');
            $table->string('comment')->nullable();
            $table->string('response')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('workplace_id');
            $table->index('price_package_id');
            $table->index('created_at');
            $table->index('status');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('workplace_id')->references('id')->on('workplaces')->onDelete('cascade');
            $table->foreign('price_package_id')->references('id')->on('price_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_requests');
        Schema::dropIfExists('workplaces');
    }
}
