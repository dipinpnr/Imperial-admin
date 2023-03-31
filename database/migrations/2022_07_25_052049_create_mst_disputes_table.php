<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_disputes', function (Blueprint $table) {
            $table->bigIncrements('dispute_id');
            $table->unsignedBigInteger('order_id');
            $table->bigInteger('issue_id')->nullable();
            $table->bigInteger('order_item_id')->nullable();
            $table->string('item_ids', 50)->nullable();
            $table->string('order_number', 191);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->date('dispute_date')->nullable();
            $table->bigInteger('store_id');
            $table->bigInteger('subadmin_id');
            $table->string('dispute_status', 46)->nullable();
            $table->text('discription')->nullable();
            $table->longText('store_response')->nullable();
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
        Schema::dropIfExists('mst_disputes');
    }
}
