<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_products', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->string('product_name', 100);
            $table->string('product_name_slug', 100);
            $table->string('product_code', 45);
            $table->unsignedBigInteger('business_type_id');
            $table->unsignedBigInteger('product_cat_id')->index('mst_store_products_product_cat_id_foreign');
            $table->bigInteger('sub_category_id')->nullable()->default(0);
            $table->date('product_offer_from_date')->nullable();
            $table->date('product_offer_to_date')->nullable();
            $table->decimal('product_price');
            $table->decimal('product_price_offer');
            $table->longText('product_description');
            $table->longText('product_specification')->nullable();
            $table->longText('product_delivery_info')->nullable();
            $table->string('product_shipping_info', 191)->nullable();
            $table->string('product_base_image', 191)->nullable();
            $table->unsignedBigInteger('store_id')->index('mst_store_products_store_id_foreign');
            $table->unsignedBigInteger('attr_group_id')->nullable();
            $table->unsignedBigInteger('attr_value_id')->nullable();
            $table->tinyInteger('product_status');
            $table->bigInteger('stock_count')->nullable();
            $table->integer('min_stock')->nullable();
            $table->decimal('product_commision_rate')->nullable();
            $table->tinyInteger('stock_status')->nullable();
            $table->bigInteger('tax_id')->nullable();
            $table->bigInteger('color_id')->nullable();
            $table->bigInteger('vendor_id')->nullable();
            $table->boolean('show_in_home_screen')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('global_product_id')->nullable();
            $table->boolean('draft')->default(false);
            $table->string('product_brand', 150)->nullable();
            $table->bigInteger('product_type')->nullable()->default(0);
            $table->bigInteger('service_type')->nullable()->default(0);
            $table->tinyInteger('is_removed')->nullable()->default(0);
            $table->tinyInteger('is_added_from_web')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_store_products');
    }
}
