<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesertasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesertas', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('user');
            $table->uuid('paket');
            $table->uuid('ujian');
            $table->string('nopes')->unique();
            $table->uuid('penguji_internal')->nullable();
            $table->uuid('penguji_external')->nullable();
            $table->timestamps();

            $table->foreign('user')->on('users')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('paket')->on('paket_soals')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('ujian')->on('ujians')->references('id')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('penguji_internal')->on('users')->references('id')->onDelete('set null')->onUpdate('no action');
            $table->foreign('penguji_external')->on('users')->references('id')->onDelete('set null')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pesertas');
    }
}
