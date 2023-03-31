<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_customers', function (Blueprint $table) {
            $table->bigIncrements('customer_id');
            $table->string('customer_first_name', 100);
            $table->string('customer_last_name', 100)->nullable();
            $table->string('customer_email', 100)->nullable();
            $table->string('customer_mobile_number', 100);
            $table->string('customer_address', 100)->nullable();
            $table->text('address_2')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->index('trn_store_customers_country_id_foreign');
            $table->unsignedBigInteger('state_id')->nullable()->index('trn_store_customers_state_id_foreign');
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('town_id')->nullable();
            $table->string('customer_location', 191)->nullable();
            $table->string('customer_pincode', 100)->nullable();
            $table->string('customer_bank_account', 100)->nullable();
            $table->string('customer_username', 100)->nullable();
            $table->string('password', 100);
            $table->tinyInteger('customer_profile_status')->nullable();
            $table->tinyInteger('customer_otp_verify_status')->nullable();
            $table->timestamps();
            $table->string('latitude', 150)->nullable();
            $table->string('longitude', 150)->nullable();
            $table->string('place', 250)->nullable();
            $table->string('place_id', 250)->nullable();
            $table->string('referral_id', 50)->nullable()->default('0');
            $table->bigInteger('referred_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_store_customers');
    }
}
