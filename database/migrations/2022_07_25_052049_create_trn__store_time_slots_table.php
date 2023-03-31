<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreTimeSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_time_slots', function (Blueprint $table) {
            $table->bigIncrements('store_time_slot_id');
            $table->unsignedBigInteger('store_id');
            $table->string('day', 191);
            $table->string('time_start', 191)->nullable();
            $table->string('time_end', 191)->nullable();
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
        Schema::dropIfExists('trn__store_time_slots');
    }
}
