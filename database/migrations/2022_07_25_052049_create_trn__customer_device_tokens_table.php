<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCustomerDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__customer_device_tokens', function (Blueprint $table) {
            $table->bigIncrements('customer_device_token_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_device_token', 191)->nullable();
            $table->string('customer_device_type', 191)->nullable();
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
        Schema::dropIfExists('trn__customer_device_tokens');
    }
}
