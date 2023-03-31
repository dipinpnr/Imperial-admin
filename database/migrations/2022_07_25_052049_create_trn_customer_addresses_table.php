<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCustomerAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_customer_addresses', function (Blueprint $table) {
            $table->bigIncrements('customer_address_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->bigInteger('state')->nullable();
            $table->bigInteger('district')->nullable();
            $table->string('street', 100)->nullable();
            $table->string('pincode', 20)->nullable();
            $table->integer('default_status')->nullable()->default(0);
            $table->string('longitude', 50)->nullable();
            $table->string('latitude', 50)->nullable();
            $table->string('place', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_customer_addresses');
    }
}
