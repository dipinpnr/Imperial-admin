<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnProductVariantAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__product_variant_attributes', function (Blueprint $table) {
            $table->bigIncrements('variant_attribute_id');
            $table->unsignedBigInteger('product_varient_id')->nullable();
            $table->unsignedBigInteger('attr_group_id')->nullable();
            $table->unsignedBigInteger('attr_value_id')->nullable();
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
        Schema::dropIfExists('trn__product_variant_attributes');
    }
}
