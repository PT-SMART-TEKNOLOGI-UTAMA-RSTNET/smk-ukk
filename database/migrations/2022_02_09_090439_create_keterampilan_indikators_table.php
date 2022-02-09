<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeterampilanIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keterampilan_indikators', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('komponen');
            $table->text('indikator');
            $table->timestamps();

            $table->foreign('komponen')->on('keterampilan_komponents')->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keterampilan_indikators');
    }
}
