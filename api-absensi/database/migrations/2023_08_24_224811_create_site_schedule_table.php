<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->string('customer_code', 20)->nullable();
            $table->string('schedule_name', 50)->nullable();
            $table->string('schedule_code', 50)->nullable();
            $table->time('schedule_in', 200)->nullable();
            $table->time('schedule_out', 200)->nullable();
            $table->string('day', 200)->nullable();
            $table->string('weekday', 200)->nullable();
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
        Schema::dropIfExists('site_schedule');
    }
}
