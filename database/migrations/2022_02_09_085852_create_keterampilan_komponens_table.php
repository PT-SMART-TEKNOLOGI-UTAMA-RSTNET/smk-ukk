<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeterampilanKomponensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keterampilan_komponents', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('paket');
            $table->string('komponen')->default('persiapan')->comment('persiapan,pelaksanaan,hasil');
            $table->string('name');
            $table->integer('nomor')->default(1);
            $table->integer('nilai_sangat_baik')->default(3);
            $table->integer('nilai_baik')->default(2);
            $table->integer('nilai_cukup')->default(1);
            $table->integer('nilai_tidak')->default(0);
            $table->string('penguji_type')->default('internal')->comment('internal,external');
            $table->timestamps();

            $table->foreign('paket')->on('paket_soals')->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keterampilan_komponents');
    }
}
