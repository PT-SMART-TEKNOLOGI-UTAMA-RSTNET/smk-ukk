<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengetahuanIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengetahuan_indikators', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('komponen');
            $table->integer('nomor')->default(1);
            $table->longText('content');
            $table->timestamps();

            $table->foreign('komponen')->on('pengetahuan_komponens')->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengetahuan_indikators');
    }
}
