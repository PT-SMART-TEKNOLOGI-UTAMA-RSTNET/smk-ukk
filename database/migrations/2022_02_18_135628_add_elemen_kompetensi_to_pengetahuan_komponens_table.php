<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElemenKompetensiToPengetahuanKomponensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pengetahuan_komponens', function (Blueprint $table) {
            $table->text('elemen_kompetensi')->nullable();
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
            $table->dropColumn('elemen_kompetensi');
        });
    }
}
