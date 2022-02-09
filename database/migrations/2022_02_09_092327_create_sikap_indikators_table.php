<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSikapIndikatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sikap_indikators', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('paket');
            $table->string('indikator');
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
        Schema::dropIfExists('sikap_indikators');
    }
}
