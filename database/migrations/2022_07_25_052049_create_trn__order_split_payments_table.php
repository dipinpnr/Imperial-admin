<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderSplitPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__order_split_payments', function (Blueprint $table) {
            $table->bigIncrements('osp_id');
            $table->unsignedBigInteger('opt_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->bigInteger('vendorId')->nullable();
            $table->unsignedBigInteger('settlementId')->nullable();
            $table->bigInteger('splitAmount')->nullable();
            $table->decimal('serviceCharge', 10)->nullable();
            $table->decimal('serviceTax', 10)->nullable();
            $table->decimal('splitServiceCharge', 10)->nullable();
            $table->decimal('splitServiceTax', 10)->nullable();
            $table->decimal('settlementAmount', 10)->nullable();
            $table->timestamp('settlementEligibilityDate')->nullable();
            $table->tinyInteger('paymentRole')->default(0);
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
        Schema::dropIfExists('trn__order_split_payments');
    }
}
