<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstAttributeValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_attribute_values', function (Blueprint $table) {
            $table->bigIncrements('attr_value_id');
            $table->string('group_value', 191);
            $table->bigInteger('attribute_group_id');
            $table->string('Hexvalue', 100)->nullable();
            $table->tinyInteger('attr_value_status');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
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
        Schema::dropIfExists('mst_attribute_values');
    }
}
