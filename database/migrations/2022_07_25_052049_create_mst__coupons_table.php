<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__coupons', function (Blueprint $table) {
            $table->bigIncrements('coupon_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('coupon_code', 191)->nullable();
            $table->unsignedBigInteger('coupon_type')->nullable();
            $table->decimal('min_purchase_amt')->nullable()->default(0);
            $table->unsignedBigInteger('discount_type')->nullable();
            $table->decimal('discount', 10)->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->unsignedBigInteger('coupon_status')->default(0);
            $table->timestamps();
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
        Schema::dropIfExists('mst__coupons');
    }
}
