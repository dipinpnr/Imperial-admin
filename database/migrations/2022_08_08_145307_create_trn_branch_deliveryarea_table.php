<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnBranchDeliveryareaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_branch_deliveryarea', function (Blueprint $table) {
            $table->unsignedBigInteger('area');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('branch_id')->on('mst_branches');
            $table->foreign('area')->references('area_id')->on('mst_delivery_areas');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trn_branch_deliveryarea', function (Blueprint $table) {
            //
        });
    }
}
