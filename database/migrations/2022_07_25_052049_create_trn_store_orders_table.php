<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_orders', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->string('order_number', 191);
            $table->tinyInteger('service_order')->default(0);
            $table->tinyInteger('service_booking_order')->default(0);
            $table->unsignedBigInteger('customer_id')->index('trn_store_orders_customer_id_foreign');
            $table->string('order_item_id', 50)->nullable()->index('trn_store_orders_order_item_id_foreign');
            $table->string('product_varient_id', 50)->nullable()->index('trn_store_orders_product_id_foreign');
            $table->string('store_id', 50)->index('trn_store_orders_store_id_foreign');
            $table->bigInteger('subadmin_id');
            $table->decimal('product_total_amount');
            $table->string('shipping_address', 191)->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->index('trn_store_orders_country_id_foreign');
            $table->unsignedBigInteger('state_id')->nullable()->index('trn_store_orders_state_id_foreign');
            $table->unsignedBigInteger('district_id')->nullable()->index('trn_store_orders_district_id_foreign');
            $table->string('quantity', 191)->nullable();
            $table->string('shipping_landmark', 191)->nullable();
            $table->string('shipping_pincode', 191)->nullable();
            $table->string('coupon_discount_percentage', 191)->nullable();
            $table->decimal('delivery_charge')->nullable();
            $table->decimal('packing_charge')->nullable();
            $table->bigInteger('time_slot')->nullable();
            $table->bigInteger('delivery_address')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time', 50)->nullable();
            $table->unsignedBigInteger('payment_type_id')->nullable()->index('trn_store_orders_payment_type_id_foreign');
            $table->unsignedBigInteger('status_id')->nullable()->index('trn_store_orders_status_id_foreign');
            $table->bigInteger('payment_status')->nullable();
            $table->bigInteger('delivery_status_id')->nullable();
            $table->unsignedBigInteger('delivery_boy_id')->nullable();
            $table->smallInteger('delivery_accept')->nullable();
            $table->string('order_code', 100)->nullable();
            $table->text('order_note')->nullable();
            $table->decimal('unit_price')->nullable();
            $table->string('order_type', 15)->nullable();
            $table->bigInteger('coupon_id')->nullable()->default(0);
            $table->string('coupon_code', 200)->nullable();
            $table->decimal('amount_reduced_by_coupon', 19)->nullable();
            $table->decimal('reward_points_used', 19)->nullable();
            $table->decimal('amount_before_applying_rp', 19)->nullable();
            $table->decimal('amount_reduced_by_rp', 19)->nullable();
            $table->string('trn_id', 250)->nullable();
            $table->timestamps();
            $table->tinyInteger('is_split_data_saved')->nullable()->default(0);
            $table->string('referenceId', 20)->nullable();
            $table->dateTime('txTime', 6)->nullable();
            $table->string('txMsg', 200)->nullable();
            $table->decimal('orderAmount', 10)->nullable();
            $table->string('txStatus', 45)->nullable();
            $table->tinyInteger('isRefunded')->nullable()->default(0);
            $table->string('refundStatus', 45)->nullable();
            $table->string('refundId', 45)->nullable();
            $table->bigInteger('store_admin_id')->nullable();
            $table->string('refundNote', 45)->nullable();
            $table->string('refundProcessStatus', 45)->nullable();
            $table->date('refundStartDate')->nullable();
            $table->date('refundProcessDate')->nullable();
            $table->string('is_locked', 45)->nullable()->default('0');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_store_orders');
    }
}
