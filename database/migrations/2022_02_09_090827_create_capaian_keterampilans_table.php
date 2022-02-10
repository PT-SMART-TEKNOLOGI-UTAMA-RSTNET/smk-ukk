<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapaianKeterampilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capaian_keterampilans', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('indikator');
            $table->uuid('peserta');
            $table->tinyInteger('nilai')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('indikator')->on('keterampilan_indikators')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('peserta')->on('pesertas')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('created_by')->on('users')->references('id')->onDelete('set null')->onUpdate('no action');
            $table->foreign('updated_by')->on('users')->references('id')->onDelete('set null')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capaian_keterampilans');
    }
}
