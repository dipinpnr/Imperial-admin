<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__order_payment_transactions', function (Blueprint $table) {
            $table->bigIncrements('opt_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('paymentMode', 40)->nullable();
            $table->bigInteger('PGOrderId')->nullable();
            $table->timestamp('txTime')->nullable();
            $table->bigInteger('referenceId')->nullable();
            $table->string('txMsg', 130)->nullable();
            $table->decimal('orderAmount', 10)->nullable();
            $table->string('txStatus', 30)->nullable()->default('0');
            $table->timestamps();
            $table->tinyInteger('payment_mode_flag')->nullable()->default(0);
            $table->tinyInteger('isFullPaymentToAdmin')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__order_payment_transactions');
    }
}
