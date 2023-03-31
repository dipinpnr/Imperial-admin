<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSysDeliveryBoyAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_delivery_boy_availabilities', function (Blueprint $table) {
            $table->bigIncrements('availability_id');
            $table->string('availabilable_days', 191);
            $table->time('availabilable_time');
            $table->tinyInteger('active_flag');
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
        Schema::dropIfExists('sys_delivery_boy_availabilities');
    }
}
