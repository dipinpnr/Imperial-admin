<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_settings', function (Blueprint $table) {
            $table->bigIncrements('store_setting_id');
            $table->unsignedBigInteger('store_id');
            $table->decimal('service_start', 10, 1)->nullable();
            $table->decimal('service_end', 10, 1)->nullable();
            $table->decimal('delivery_charge', 10)->nullable();
            $table->decimal('packing_charge', 10)->nullable();
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
        Schema::dropIfExists('trn_store_settings');
    }
}
