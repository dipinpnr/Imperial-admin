<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnDeliveryBoyLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__delivery_boy_locations', function (Blueprint $table) {
            $table->bigIncrements('dbl_id');
            $table->unsignedBigInteger('delivery_boy_id')->nullable();
            $table->string('latitude', 191)->nullable();
            $table->string('longitude', 191)->nullable();
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
        Schema::dropIfExists('trn__delivery_boy_locations');
    }
}
