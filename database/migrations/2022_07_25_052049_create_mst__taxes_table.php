<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__taxes', function (Blueprint $table) {
            $table->bigIncrements('tax_id');
            $table->string('tax_name', 50)->nullable();
            $table->decimal('tax_value', 11);
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('is_removed')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__taxes');
    }
}
