<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreProductVarientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_product_varients', function (Blueprint $table) {
            $table->bigIncrements('product_varient_id');
            $table->unsignedBigInteger('product_id')->index('mst_store_product_varients_product_id_foreign');
            $table->unsignedBigInteger('store_id')->index('mst_store_product_varients_store_id_foreign');
            $table->string('variant_name', 250)->nullable();
            $table->decimal('product_varient_price')->nullable();
            $table->decimal('product_varient_offer_price')->nullable();
            $table->date('product_varient_offer_from_date')->nullable();
            $table->date('product_varient_offer_to_date')->nullable();
            $table->string('product_varient_base_image', 191)->nullable();
            $table->unsignedBigInteger('attr_group_id')->nullable();
            $table->unsignedBigInteger('attr_value_id')->nullable();
            $table->integer('stock_count')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 6);
            $table->tinyInteger('is_removed')->nullable()->default(0);
            $table->tinyInteger('is_base_variant')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_store_product_varients');
    }
}
