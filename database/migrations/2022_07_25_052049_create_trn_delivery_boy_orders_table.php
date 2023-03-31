<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnDeliveryBoyOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_delivery_boy_orders', function (Blueprint $table) {
            $table->bigIncrements('delivery_boy_order_id');
            $table->string('order_item_id', 100)->index('trn_delivery_boy_orders_order_item_id_foreign');
            $table->unsignedBigInteger('order_id')->index('trn_delivery_boy_orders_order_id_foreign');
            $table->string('store_id', 100)->index('trn_delivery_boy_orders_store_id_foreign');
            $table->string('delivery_boy_id', 100)->nullable()->index('trn_delivery_boy_orders_status_id_foreign');
            $table->dateTime('assigned_date_time');
            $table->dateTime('delivery_date_time');
            $table->dateTime('Expected_date_time');
            $table->unsignedTinyInteger('delivery_status_id');
            $table->string('payment_status', 100);
            $table->bigInteger('payment_type_id')->index('trn_delivery_boy_orders_payment_type_id_foreign');
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
        Schema::dropIfExists('trn_delivery_boy_orders');
    }
}
