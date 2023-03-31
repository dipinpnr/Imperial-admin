<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkAgencyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_agency', function (Blueprint $table) {
            $table->bigIncrements('link_id');
            $table->unsignedBigInteger('store_id')->index('mst_store_link_agency_store_id_foreign');
            $table->unsignedBigInteger('agency_id')->index('mst_store_link_agency_agency_id_foreign');
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
        Schema::dropIfExists('mst_store_link_agency');
    }
}
