<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartEndDateWorkhour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workhour', function (Blueprint $table) {
            $table->date('schedule_start_date')->nullable()->after('workhour_code');
            $table->date('schedule_end_date')->nullable()->after('schedule_start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workhour', function (Blueprint $table) {
            $table->dropColumn('schedule_start_date');
            $table->dropColumn('schedule_end_date');
        });
    }
}
