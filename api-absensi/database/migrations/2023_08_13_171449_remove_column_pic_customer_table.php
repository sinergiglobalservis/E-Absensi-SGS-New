<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnPicCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_customer', function (Blueprint $table) {
            $table->dropColumn('pic1');
            $table->dropColumn('pic1_phone');
            $table->dropColumn('pic1_email');
            $table->dropColumn('pic2');
            $table->dropColumn('pic2_phone');
            $table->dropColumn('pic2_email');
            $table->dropColumn('start_contract');
            $table->dropColumn('end_contract');
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
            $table->string('pic1')->nullable();
            $table->string('pic1_phone')->nullable();
            $table->string('pic1_email')->nullable();
            $table->string('pic2')->nullable();
            $table->string('pic2_phone')->nullable();
            $table->string('pic2_email')->nullable();
            $table->date('start_contract')->nullable();
            $table->date('end_contract')->nullable();
        });
    }
}