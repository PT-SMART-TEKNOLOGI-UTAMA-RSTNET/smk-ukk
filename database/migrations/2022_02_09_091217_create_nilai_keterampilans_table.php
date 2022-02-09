<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNilaiKeterampilansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_keterampilans', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('siswa');
            $table->uuid('paket');
            $table->integer('nilai')->default(0);
            $table->timestamps();

            $table->foreign('siswa')->on('users')->references('id')->onDelete('cascade')->onUpdate('no action');
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
        Schema::dropIfExists('nilai_keterampilans');
    }
}
