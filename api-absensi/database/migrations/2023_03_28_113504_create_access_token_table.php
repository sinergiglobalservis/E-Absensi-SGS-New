<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_token', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email',100);
            $table->string('access_token',512);
            $table->string('ip_address', 30);
            $table->string('log_from');
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->tinyInteger('status')->default(1)->comment("{0:inactive, 1:active, 2:force_drop}");
            $table->datetime('until_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_token');
    }
}
