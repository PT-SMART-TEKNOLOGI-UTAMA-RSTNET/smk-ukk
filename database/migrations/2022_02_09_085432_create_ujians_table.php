<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUjiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('jurusan');
            $table->string('name');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->timestamps();

            switch (config('database.erapor')){
                case 'mysql_erapor' :
                    $eraporDb = DB::connection('mysql_erapor')->getDatabaseName();
                    break;
                case 'pgsql_erapor' :
                    $eraporDb = DB::connection('pgsql_erapor')->getDatabaseName();
                    break;
            }
            $table->foreign('jurusan')->on(new Expression($eraporDb . '.jurusans'))->references('id')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ujians');
    }
}
