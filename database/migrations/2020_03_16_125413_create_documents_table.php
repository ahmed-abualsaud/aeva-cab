<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('notes')->nullable();
            $table->date('expires_on')->nullable();
            $table->morphs('documentable');
            $table->enum('status', ['In review', 'Approved', 'Rejected'])->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')
                ->references('id')
                ->on('admins')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
