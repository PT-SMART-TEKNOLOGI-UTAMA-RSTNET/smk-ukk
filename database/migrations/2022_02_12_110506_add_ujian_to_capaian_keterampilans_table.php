<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUjianToCapaianKeterampilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capaian_keterampilans', function (Blueprint $table) {
            $table->uuid('ujian');
            $table->foreign('ujian')->on('ujians')->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capaian_keterampilans', function (Blueprint $table) {
            $table->dropForeign('capaian_keterampilans_ujian_foreign');
            $table->dropColumn('ujian');
        });
    }
}
