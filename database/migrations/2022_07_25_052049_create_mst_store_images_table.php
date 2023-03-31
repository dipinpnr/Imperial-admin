<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_images', function (Blueprint $table) {
            $table->bigIncrements('store_image_id');
            $table->string('store_image', 191);
            $table->unsignedBigInteger('store_id')->index('mst_store_images_store_id_foreign');
            $table->boolean('default_image')->default(false);
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
        Schema::dropIfExists('mst_store_images');
    }
}
