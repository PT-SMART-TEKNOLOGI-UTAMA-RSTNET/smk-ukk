<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUjianToSikapCapaiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sikap_capaians', function (Blueprint $table) {
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
        Schema::table('sikap_capaians', function (Blueprint $table) {
            $table->dropForeign('sikap_capaians_ujian_foreign');
            $table->dropColumn('ujian');
        });
    }
}
