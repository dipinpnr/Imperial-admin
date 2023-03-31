<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_companies', function (Blueprint $table) {
            $table->bigIncrements('company_id');
            $table->string('company_name', 45)->unique();
            $table->string('company_name_slug', 45)->unique();
            $table->string('company_contact_person_name', 45);
            $table->string('company_contact_person_phone_number', 45);
            $table->string('company_contact_number_2', 45)->nullable();
            $table->string('company_website_link', 45)->nullable();
            $table->string('company_pincode', 25)->nullable();
            $table->longText('company_primary_address');
            $table->string('company_email_address', 45)->nullable();
            $table->string('company_username', 45);
            $table->string('company_password', 100);
            $table->string('company_logo', 191)->nullable();
            $table->tinyInteger('company_account_status');
            $table->unsignedBigInteger('country_id')->index('mst_store_companies_country_id_foreign');
            $table->unsignedBigInteger('business_type_id')->index('mst_store_companies_business_type_id_foreign');
            $table->unsignedBigInteger('state_id')->index('mst_store_companies_state_id_foreign');
            $table->unsignedBigInteger('district_id')->index('mst_store_companies_district_id_foreign');
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
        Schema::dropIfExists('mst_store_companies');
    }
}
