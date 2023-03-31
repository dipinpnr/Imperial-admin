<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_agencies', function (Blueprint $table) {
            $table->bigIncrements('agency_id');
            $table->string('agency_name', 45)->unique();
            $table->string('agency_name_slug', 45)->unique();
            $table->string('agency_contact_person_name', 45);
            $table->string('agency_contact_person_phone_number', 45);
            $table->string('agency_contact_number_2', 100)->nullable();
            $table->string('agency_website_link', 100)->nullable();
            $table->string('agency_pincode', 50)->nullable();
            $table->longText('agency_primary_address');
            $table->string('agency_email_address', 45);
            $table->string('agency_username', 45);
            $table->string('agency_password', 100);
            $table->string('agency_logo', 191)->nullable();
            $table->tinyInteger('agency_account_status');
            $table->unsignedBigInteger('country_id')->index('mst_store_agencies_country_id_foreign');
            $table->unsignedBigInteger('business_type_id')->index('mst_store_agencies_business_type_id_foreign');
            $table->unsignedBigInteger('state_id')->index('mst_store_agencies_state_id_foreign');
            $table->unsignedBigInteger('district_id')->index('mst_store_agencies_district_id_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_store_agencies');
    }
}
