<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnTaxSplitUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__tax_split_ups', function (Blueprint $table) {
            $table->bigIncrements('tax_split_up_id');
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->string('split_tax_name', 50)->nullable();
            $table->decimal('split_tax_value', 11)->nullable();
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
        Schema::dropIfExists('trn__tax_split_ups');
    }
}
