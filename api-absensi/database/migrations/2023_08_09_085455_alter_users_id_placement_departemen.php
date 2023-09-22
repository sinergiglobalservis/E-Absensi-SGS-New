<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterUsersIdPlacementDepartemen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE placement_departemen ALTER COLUMN 
                  users_id TYPE bigint USING (users_id)::bigint');
        Schema::table('placement_departemen', function (Blueprint $table) {
            $table->bigInteger('users_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('placement_departemen', function (Blueprint $table) {
            //
        });
    }
}