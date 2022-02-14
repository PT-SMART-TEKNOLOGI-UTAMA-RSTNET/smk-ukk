<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerToPengetahuanKomponensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengetahuan_komponens', function (Blueprint $table) {
            $table->uuid('answer')->nullable();
            $table->foreign('answer')->on('pengetahuan_indikators')->references('id')->onDelete('set null')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengetahuan_komponens', function (Blueprint $table) {
            $table->dropForeign('pengetahuan_komponens_answer_foreign');
            $table->dropColumn('answer');
        });
    }
}
