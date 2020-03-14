<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTripStartEndDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partner_trips', function (Blueprint $table) {
            
            if (env('DB_CONNECTION') === 'mysql'){
                $table->renameColumn('startDate', 'start_date');
                $table->renameColumn('endDate', 'end_date');
            } else if (env('DB_CONNECTION') === 'pgsql'){
                // PostgreSQL
                DB::statement("ALTER TABLE partner_trips RENAME COLUMN startDate TO start_date");
                DB::statement("ALTER TABLE partner_trips RENAME COLUMN endDate TO end_date");
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partner_trips', function (Blueprint $table) {
            $table->renameColumn('start_date', 'startDate');
            $table->renameColumn('end_date', 'endDate');
        });
    }
}
