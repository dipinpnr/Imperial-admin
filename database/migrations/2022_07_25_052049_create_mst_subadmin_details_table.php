<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstSubadminDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_subadmin_details', function (Blueprint $table) {
            $table->bigIncrements('subadmin_details_id');
            $table->text('subadmin_address');
            $table->decimal('subadmin_commision_amount', 10);
            $table->bigInteger('phone')->nullable();
            $table->decimal('subadmin_commision_percentage', 10);
            $table->unsignedBigInteger('subadmin_id');
            $table->bigInteger('country_id');
            $table->bigInteger('state_id');
            $table->bigInteger('district_id');
            $table->bigInteger('town_id');
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
        Schema::dropIfExists('mst_subadmin_details');
    }
}
