<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreOtpVerifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_otp_verify', function (Blueprint $table) {
            $table->bigIncrements('store_otp_verify_id');
            $table->unsignedBigInteger('store_id')->index('trn_store_otp_verify_store_id_foreign');
            $table->string('store_otp_expirytime', 45);
            $table->string('store_otp', 45);
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
        Schema::dropIfExists('trn_store_otp_verify');
    }
}
