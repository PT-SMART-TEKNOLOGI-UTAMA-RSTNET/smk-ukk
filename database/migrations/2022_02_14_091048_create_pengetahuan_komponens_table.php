<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengetahuanKomponensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengetahuan_komponens', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('paket');
            $table->integer('nomor')->default(1);
            $table->longText('content')->nullable();
            $table->string('type')->default('pg')->comment('pg,essay');
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
        Schema::dropIfExists('pengetahuan_komponens');
    }
}
