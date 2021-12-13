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
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->date('license_expires_on')->nullable();
            $table->string('avatar')->nullable();
            $table->string('city')->nullable();
            $table->string('vehicle')->nullable();
            $table->unsignedBigInteger('fleet_id')->nullable();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->decimal('rating', 4, 2)->default(5);
            $table->boolean('status')->default(0);
            $table->enum('cab_status', ['ONLINE', 'RIDING', 'OFFLINE'])->default(0);
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('device_id')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fleet_id');
            $table->index('created_at');

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');
            $table->foreign('fleet_id')->references('id')->on('fleets')->onDelete('cascade');
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
