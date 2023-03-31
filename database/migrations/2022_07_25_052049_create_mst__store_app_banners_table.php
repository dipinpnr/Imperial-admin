<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreAppBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__store_app_banners', function (Blueprint $table) {
            $table->bigIncrements('banner_id');
            $table->bigInteger('town_id')->nullable();
            $table->text('image');
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__store_app_banners');
    }
}
