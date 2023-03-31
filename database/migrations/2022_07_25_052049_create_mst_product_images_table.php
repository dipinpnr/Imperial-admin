<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_product_images', function (Blueprint $table) {
            $table->increments('product_image_id');
            $table->string('product_image', 250);
            $table->integer('image_flag')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable()->index('mst_product_images_product_varient_id_foreign');
            $table->unsignedBigInteger('product_id')->nullable();
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
        Schema::dropIfExists('mst_product_images');
    }
}
