<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_stores', function (Blueprint $table) {
            $table->bigIncrements('store_id');
            $table->string('store_name', 45)->unique();
            $table->string('store_mobile', 191)->nullable();
            $table->string('store_name_slug', 45)->unique();
            $table->string('store_contact_person_name', 45)->nullable();
            $table->string('store_contact_person_phone_number', 45)->nullable();
            $table->string('store_contact_number_2', 100)->nullable();
            $table->string('store_website_link', 191)->nullable();
            $table->string('store_pincode', 50)->nullable();
            $table->longText('store_primary_address')->nullable();
            $table->string('email', 45)->nullable();
            $table->rememberToken();
            $table->unsignedBigInteger('store_country_id')->nullable()->index('mst_stores_store_country_id_foreign');
            $table->unsignedBigInteger('store_state_id')->nullable()->index('mst_stores_store_state_id_foreign');
            $table->unsignedBigInteger('store_district_id')->nullable()->index('mst_stores_store_district_id_foreign');
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->decimal('store_commision_percentage')->nullable();
            $table->tinyInteger('store_added_by')->nullable();
            $table->string('store_username', 45)->nullable();
            $table->string('password', 100);
            $table->tinyInteger('store_account_status');
            $table->tinyInteger('store_otp_verify_status');
            $table->timestamps();
            $table->softDeletes();
            $table->string('place', 45)->nullable();
            $table->unsignedBigInteger('town_id')->nullable()->index('mst_stores_town_id_foreign');
            $table->decimal('store_commision_amount')->nullable();
            $table->unsignedBigInteger('subadmin_id')->default(0)->index('mst_stores_subadmin_id_foreign');
            $table->string('store_qrcode', 200)->nullable();
            $table->decimal('service_area', 10)->nullable();
            $table->string('order_number_prefix', 50)->nullable();
            $table->boolean('online_status')->nullable()->default(false);
            $table->string('profile_image', 250)->nullable();
            $table->string('upi_id', 100)->nullable();
            $table->string('latitude', 150)->nullable();
            $table->string('longitude', 150)->nullable();
            $table->string('place_id', 250)->nullable();
            $table->tinyInteger('is_pgActivated')->nullable();
            $table->string('gst', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_stores');
    }
}
