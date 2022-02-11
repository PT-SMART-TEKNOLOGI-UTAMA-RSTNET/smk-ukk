<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToUjiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->string('token',10)->nullable();
            $table->dateTime('token_expired_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn(['token','token_expired','is_active','description']);
        });
    }
}
