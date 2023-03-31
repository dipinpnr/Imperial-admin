<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreWebTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_web_tokens', function (Blueprint $table) {
            $table->bigIncrements('store_web_token_id');
            $table->unsignedBigInteger('store_admin_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('store_web_token', 191)->nullable();
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
        Schema::dropIfExists('trn__store_web_tokens');
    }
}
