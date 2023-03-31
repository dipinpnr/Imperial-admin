<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreBankDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_bank_data', function (Blueprint $table) {
            $table->bigIncrements('store_bank_data_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('ifsc', 15)->nullable();
            $table->string('account_holder', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('upi_vpa', 50)->nullable();
            $table->string('upi_account_holder', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('vendor_name', 100)->nullable();
            $table->string('vendor_id', 100)->nullable();
            $table->string('settlement_cycle_id', 100)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('trn__store_bank_data');
    }
}
