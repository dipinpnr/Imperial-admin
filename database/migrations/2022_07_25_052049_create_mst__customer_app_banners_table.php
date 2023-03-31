<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstCustomerAppBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__customer_app_banners', function (Blueprint $table) {
            $table->bigIncrements('banner_id');
            $table->bigInteger('town_id')->nullable();
            $table->text('image');
            $table->smallInteger('status')->nullable();
            $table->bigInteger('store_id')->nullable()->default(0);
            $table->integer('default_status');
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
        Schema::dropIfExists('mst__customer_app_banners');
    }
}
