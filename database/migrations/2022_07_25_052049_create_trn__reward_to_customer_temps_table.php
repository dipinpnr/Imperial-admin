<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnRewardToCustomerTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__reward_to_customer_temps', function (Blueprint $table) {
            $table->bigIncrements('reward_to_customer_temp_id');
            $table->string('customer_mobile_number', 191)->nullable();
            $table->text('reward_discription')->nullable();
            $table->integer('reward_points')->default(0);
            $table->boolean('reward_status')->default(false);
            $table->date('added_date')->nullable();
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
        Schema::dropIfExists('trn__reward_to_customer_temps');
    }
}
