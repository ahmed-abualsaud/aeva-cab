<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddRequestIdToTracesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('traces', function (Blueprint $table) {
          $table->foreignId('request_id')->after('event')->nullable()->constrained('cab_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('traces', function (Blueprint $table) {
            $indexes = collect(DB::select('SHOW INDEX FROM traces;'))->pluck('Key_name','Column_name');
            $indexes->has('request_id') and $table->dropForeign($indexes['request_id']);
            $table->dropColumn('request_id');
        });
    }
}
