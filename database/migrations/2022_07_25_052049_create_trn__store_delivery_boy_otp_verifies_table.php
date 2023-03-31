<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreDeliveryBoyOtpVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_delivery_boy_otp_verifies', function (Blueprint $table) {
            $table->bigIncrements('store_delivery_boy_otp_verify_id');
            $table->unsignedBigInteger('delivery_boy_id');
            $table->string('otp_expirytime', 45);
            $table->string('otp', 45);
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
        Schema::dropIfExists('trn__store_delivery_boy_otp_verifies');
    }
}
