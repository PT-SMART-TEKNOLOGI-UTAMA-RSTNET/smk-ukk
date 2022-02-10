<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('siswa')->nullable();
            $table->uuid('jurusan')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('user_type')->default('admin')->comment('admin,penguji,siswa');
            $table->string('penguji_type')->nullable()->comment('internal,external');
            $table->string('nis',40)->nullable();
            $table->string('nopes',60)->nullable();
            $table->string('nisn',50)->nullable();
            $table->rememberToken();
            $table->timestamps();

            switch (config('database.erapor')){
                case 'mysql_erapor' :
                    $eraporDb = DB::connection('mysql_erapor')->getDatabaseName();
                    break;
                case 'pgsql_erapor' :
                    $eraporDb = DB::connection('pgsql_erapor')->getDatabaseName();
                    break;
            }
            $table->foreign('siswa')->on(new Expression($eraporDb . '.siswas'))->references('id')->onDelete('set null')->onUpdate('no action');
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
        Schema::dropIfExists('users');
    }
}
