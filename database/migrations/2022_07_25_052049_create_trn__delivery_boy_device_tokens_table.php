<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnDeliveryBoyDeviceTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__delivery_boy_device_tokens', function (Blueprint $table) {
            $table->bigIncrements('delivery_boy_device_token_id');
            $table->unsignedBigInteger('delivery_boy_id')->nullable();
            $table->string('dboy_device_token', 191)->nullable();
            $table->string('dboy_device_type', 191)->nullable();
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
        Schema::dropIfExists('trn__delivery_boy_device_tokens');
    }
}
