<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreCustomerOtpVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_customer_otp_verify', function (Blueprint $table) {
            $table->bigIncrements('customer_otp_verify_id');
            $table->unsignedBigInteger('customer_id')->index('trn_store_customer_otp_verify_customer_id_foreign');
            $table->string('customer_otp_expirytime', 45);
            $table->string('customer_otp', 45);
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
        Schema::dropIfExists('trn_store_customer_otp_verify');
    }
}
