<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('attendee_date')->nullable();
            $table->time('attendee_time_in')->nullable();
            $table->string('attendee_longitude_in')->nullable();
            $table->string('attendee_latitude_in')->nullable();
            $table->string('images_in', 512)->nullable();
            $table->time('attendee_time_out')->nullable();
            $table->string('attendee_longitude_out')->nullable();
            $table->string('attendee_latitude_out')->nullable();
            $table->string('workhour_code')->nullable();
            $table->string('images_out', 512)->nullable();
            $table->integer('absence_ref')->nullable()->default(null);
            $table->string('absence_ref_desc', 512)->nullable();
            $table->bigInteger('users_id');

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
        Schema::dropIfExists('attendance');
    }
}
