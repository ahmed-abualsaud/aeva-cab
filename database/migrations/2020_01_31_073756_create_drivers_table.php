<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs('concat(first_name," ",last_name)');
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('secondary_phone')->unique()->nullable();
            $table->enum('title', ['Normal', 'AevaX'])->default('Normal');
            $table->date('license_expires_on')->nullable();
            $table->string('avatar')->nullable();
            $table->string('city')->nullable();
            $table->string('vehicle')->nullable();
            $table->unsignedBigInteger('fleet_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('car_type_id')->nullable();
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->decimal('rating', 4, 2)->default(5);
            $table->boolean('status')->default(true);
            $table->enum('cab_status', ['Online', 'Riding', 'Offline'])->default('Offline');
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();
            $table->boolean('approved')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('fleet_id');
            $table->index('created_at');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('set null');
            $table->foreign('fleet_id')->references('id')->on('fleets')->onDelete('set null');
            $table->foreign('car_type_id')->references('id')->on('car_types')->onDelete('set null');
            $table->foreign('referrer_id')->references('id')->on('drivers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
