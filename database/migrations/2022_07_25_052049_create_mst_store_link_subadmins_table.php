<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreLinkSubadminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_link_subadmins', function (Blueprint $table) {
            $table->bigIncrements('store_link_subadmin_id');
            $table->unsignedBigInteger('store_id')->index('mst_store_link_subadmins_store_id_foreign');
            $table->unsignedBigInteger('subadmin_id')->index('mst_store_link_subadmins_subadmin_id_foreign');
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
        Schema::dropIfExists('mst_store_link_subadmins');
    }
}
