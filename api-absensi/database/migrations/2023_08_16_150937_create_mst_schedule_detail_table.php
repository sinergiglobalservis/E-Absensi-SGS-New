<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstScheduleDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_schedule_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mst_schedule_code', 20);
            $table->string('schedule_code', 20)->nullable();
            $table->string('schedule_name', 100)->nullable();
            $table->string('day', 100)->nullable();
            $table->time('schedule_in')->nullable();
            $table->time('schedule_out')->nullable();
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
        Schema::dropIfExists('mst_schedule_detail');
    }
}
