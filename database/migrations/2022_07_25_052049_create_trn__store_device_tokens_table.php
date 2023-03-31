<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_device_tokens', function (Blueprint $table) {
            $table->bigIncrements('store_device_token_id');
            $table->unsignedBigInteger('store_admin_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('store_device_token', 191)->nullable();
            $table->string('store_device_type', 191)->nullable();
            $table->timestamps();
            $table->string('store_device_id', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__store_device_tokens');
    }
}
