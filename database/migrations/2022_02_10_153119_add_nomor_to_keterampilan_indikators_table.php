<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorToKeterampilanIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keterampilan_indikators', function (Blueprint $table) {
            $table->integer('nomor')->after('komponen')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keterampilan_indikators', function (Blueprint $table) {
            $table->dropColumn('nomor');
        });
    }
}
