<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCustomerCodeMasterWorkhour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mst_workhour', function (Blueprint $table) {
            $table->renameColumn('customer_code', 'template_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_workhour', function (Blueprint $table) {
            $table->renameColumn('template_name', 'customer_code');
        });
    }
}
