<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStartAndEndDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absence', function (Blueprint $table) {
            $table->datetime('absence_start_date')->after('absence_date');
            $table->datetime('absence_end_date')->after('absence_start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absence', function (Blueprint $table) {
            //
        });
    }
}
