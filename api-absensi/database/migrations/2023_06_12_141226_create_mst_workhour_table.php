<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstWorkhourTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_workhour', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_code', 20);
            $table->string('workhour_code', 20);
            $table->string('workhour_name', 512);
            $table->time('workhour_in');
            $table->time('workhour_out');
            $table->bigInteger('total_hour')->comment("{total working day in one day}");
            $table->bigInteger('total_day')->comment("{total working day in one week}");
            $table->string('keterangan', 512);

            // audit management
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_workhour');
    }
}
