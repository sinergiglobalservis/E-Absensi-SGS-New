<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScheduleMasterCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_customer', function (Blueprint $table) {
            $table->string('schedule_list', 500)->nullable(); // get schedule from master workhour
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_customer', function (Blueprint $table) {
            $table->dropColumn('schedule_list');
        });
    }
}
