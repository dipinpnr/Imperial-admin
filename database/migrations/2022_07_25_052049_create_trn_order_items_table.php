<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_order_items', function (Blueprint $table) {
            $table->bigIncrements('order_item_id');
            $table->bigInteger('order_id')->nullable();
            $table->string('cart_id', 45)->nullable();
            $table->bigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable()->index('trn_order_items_product_varient_id_foreign');
            $table->unsignedBigInteger('customer_id')->nullable()->index('trn_order_items_customer_id_foreign');
            $table->unsignedBigInteger('store_id')->nullable()->index('trn_order_items_store_id_foreign');
            $table->unsignedBigInteger('delivery_boy_id')->nullable()->index('trn_order_items_delivery_boy_id_foreign');
            $table->string('store_commision_percentage', 191)->nullable();
            $table->tinyInteger('cart_status')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->decimal('tax_amount')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->tinyInteger('delivery_status')->nullable();
            $table->string('discount_percentage', 191)->nullable();
            $table->decimal('discount_amount')->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable()->index('trn_order_items_payment_type_id_foreign');
            $table->date('order_date')->nullable();
            $table->date('pay_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->timestamps();
            $table->tinyInteger('tick_status')->nullable()->default(0);
            $table->tinyInteger('delivery_boy_tick_status')->nullable()->default(0);
            $table->softDeletes('deleted_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_order_items');
    }
}
