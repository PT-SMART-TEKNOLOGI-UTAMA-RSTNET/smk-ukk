<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapaianPengetahuansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capaian_pengetahuans', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('ujian');
            $table->uuid('peserta');
            $table->uuid('komponen');
            $table->uuid('indikator');
            $table->integer('nilai')->default(0);
            $table->timestamps();

            $table->foreign('ujian')->on('ujians')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('peserta')->on('pesertas')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('komponen')->on('pengetahuan_komponens')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('indikator')->on('pengetahuan_indikators')->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capaian_pengetahuans');
    }
}
