<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_payments', function (Blueprint $table) {
            $table->bigIncrements('payment_id');
            $table->string('order_item_id', 100)->index('trn_store_payments_order_item_id_foreign');
            $table->unsignedBigInteger('order_id')->index('trn_store_payments_order_id_foreign');
            $table->string('delivery_boy_id', 20)->index('trn_store_payments_delivery_boy_id_foreign');
            $table->unsignedBigInteger('customer_id')->index('trn_store_payments_customer_id_foreign');
            $table->unsignedBigInteger('payment_type_id')->index('trn_store_payments_payment_type_id_foreign');
            $table->string('store_id', 100)->index('trn_store_payments_store_id_foreign');
            $table->string('store_commision_percentage', 191);
            $table->string('admin_commision_amount', 191);
            $table->string('return_amount', 191);
            $table->string('total_amount', 191);
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
        Schema::dropIfExists('trn_store_payments');
    }
}
