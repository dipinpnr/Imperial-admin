<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreDeliveryTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_delivery_time_slots', function (Blueprint $table) {
            $table->bigIncrements('store_delivery_time_slot_id');
            $table->unsignedBigInteger('store_id');
            $table->string('time_start', 191);
            $table->string('time_end', 191);
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
        Schema::dropIfExists('trn__store_delivery_time_slots');
    }
}
