<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAbsenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absence', function (Blueprint $table) {
            $table->dropColumn('absence_start_date');
            $table->dropColumn('absence_end_date');
            $table->dropColumn('absence_duration');
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
            $table->datetime('absence_start_date');
            $table->datetime('absence_end_date');
            $table->integer('absence_duration');
        });
    }
}
