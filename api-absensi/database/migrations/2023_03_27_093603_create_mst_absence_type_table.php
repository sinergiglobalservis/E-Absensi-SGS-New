<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMstAbsenceTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_absence_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('absence_code', 20);
            $table->string('absence_name');
            $table->integer('absence_long_date')->default(1);
            $table->integer('absence_type')->comment("{1:cuti, 2:ijin, 3:sakit}"); // ijin atau cuti

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
        Schema::dropIfExists('mst_absence_type');
    }
}
