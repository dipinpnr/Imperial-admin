<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_branches', function (Blueprint $table) {
            $table->bigIncrements('branch_id');
            $table->integer('city_id')->references('city_id')->on('sys_cities')->onDelete('cascade');
            $table->string('branch_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('branch_contact_person')->nullable();
            $table->string('branch_contact_number')->nullable();
            $table->tintInt('branch_status')->default(1)->comment("1 => active ,0  => inactive");
            $table->string('branch_latitude')->nullable();
            $table->string('branch_longitude')->nullable();
            $table->string('branch_zip_code')->nullable();
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
        Schema::dropIfExists('mst_branches');
    }
}
