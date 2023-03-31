<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_countries', function (Blueprint $table) {
            $table->increments('country_id');
            $table->string('country_name', 191);
            $table->string('iso', 45)->nullable();
            $table->string('sortname', 50);
            $table->string('iso3', 50)->nullable();
            $table->string('numcode', 45);
            $table->string('phonecode', 45);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_countries');
    }
}
