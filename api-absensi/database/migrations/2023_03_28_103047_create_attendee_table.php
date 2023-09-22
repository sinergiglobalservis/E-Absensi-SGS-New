<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendee', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('attendee_date');
            $table->string('attendee_type', 20)->nullable(); //in or out
            $table->time('attendee_time');
            $table->string('attendee_longitude')->nullable();
            $table->string('attendee_latitude')->nullable();
            $table->string('workhour_code');
            $table->string('images', 512)->nullable();
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
        Schema::dropIfExists('attendee');
    }
}
