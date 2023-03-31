<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstGlobalProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__global_products', function (Blueprint $table) {
            $table->bigIncrements('global_product_id');
            $table->string('product_name', 200)->nullable();
            $table->string('product_name_slug', 100)->nullable();
            $table->longText('product_description')->nullable();
            $table->decimal('regular_price')->nullable()->default(0);
            $table->decimal('sale_price')->nullable()->default(0);
            $table->unsignedBigInteger('tax_id')->nullable()->default(0);
            $table->integer('min_stock')->nullable()->default(0);
            $table->string('product_code', 100)->nullable();
            $table->unsignedBigInteger('business_type_id')->nullable()->default(0);
            $table->unsignedBigInteger('color_id')->nullable()->default(0);
            $table->string('product_brand', 100)->nullable();
            $table->unsignedBigInteger('attr_group_id')->nullable()->default(0);
            $table->unsignedBigInteger('attr_value_id')->nullable()->default(0);
            $table->unsignedBigInteger('product_cat_id')->nullable()->default(0);
            $table->bigInteger('sub_category_id')->nullable()->default(0);
            $table->unsignedBigInteger('vendor_id')->nullable()->default(0);
            $table->string('product_base_image', 100)->nullable();
            $table->date('created_date')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
            $table->bigInteger('isConvertedFromProducts')->nullable()->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('mst__global_products');
    }
}
