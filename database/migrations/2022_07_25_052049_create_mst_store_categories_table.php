<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_categories', function (Blueprint $table) {
            $table->bigIncrements('category_id');
            $table->bigInteger('parent_id');
            $table->unsignedBigInteger('business_type_id');
            $table->string('category_name', 45)->unique();
            $table->string('category_name_slug', 45)->nullable()->unique();
            $table->string('category_icon', 191)->nullable();
            $table->longText('category_description')->nullable();
            $table->tinyInteger('category_status');
            $table->softDeletes();
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
        Schema::dropIfExists('mst_store_categories');
    }
}
