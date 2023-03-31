<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnTermsAndConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__terms_and_conditions', function (Blueprint $table) {
            $table->bigIncrements('tc_id');
            $table->longText('terms_and_condition')->nullable();
            $table->timestamps();
            $table->integer('role')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__terms_and_conditions');
    }
}
