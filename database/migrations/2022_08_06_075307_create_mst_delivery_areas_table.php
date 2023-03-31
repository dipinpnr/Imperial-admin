<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDeliveryAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_delivery_areas', function (Blueprint $table) {
            $table->bigIncrements('area_id');
            $table->integer('branch_id')->references('branch_id')->on('users')->onDelete('cascade');
            $table->string('area_name')->nullable();
            $table->string('area_code')->nullable();
            $table->string('area_status')->nullable();
            $table->string('area_latitude')->nullable();
            $table->string('area_longitude')->nullable();
            $table->string('area_zip_code')->nullable();
            $table->tinyInt('is_active')->default(1)->comment("1 => active ,0  => inactive");
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
        Schema::dropIfExists('mst_delivery_areas');
    }
}
