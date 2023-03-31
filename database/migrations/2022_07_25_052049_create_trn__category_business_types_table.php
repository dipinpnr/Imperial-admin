<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCategoryBusinessTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__category_business_types', function (Blueprint $table) {
            $table->bigIncrements('cbt_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('business_type_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(0);
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
        Schema::dropIfExists('trn__category_business_types');
    }
}
