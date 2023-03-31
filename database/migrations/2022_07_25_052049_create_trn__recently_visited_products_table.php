<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnRecentlyVisitedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__recently_visited_products', function (Blueprint $table) {
            $table->bigIncrements('rvp_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable();
            $table->integer('visit_count')->nullable();
            $table->bigInteger('vendor_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('sub_category_id')->nullable();
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
        Schema::dropIfExists('trn__recently_visited_products');
    }
}
