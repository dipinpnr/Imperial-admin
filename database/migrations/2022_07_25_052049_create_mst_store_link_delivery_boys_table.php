<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkDeliveryBoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_delivery_boys', function (Blueprint $table) {
            $table->bigIncrements('store_link_delivery_boy_id');
            $table->unsignedBigInteger('store_id')->index('mst_store_link_delivery_boys_store_id_foreign');
            $table->unsignedBigInteger('delivery_boy_id')->index('mst_store_link_delivery_boys_delivery_boy_id_foreign');
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
        Schema::dropIfExists('mst_store_link_delivery_boys');
    }
}
