<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCustomerRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_customer_rewards', function (Blueprint $table) {
            $table->bigIncrements('reward_id');
            $table->unsignedBigInteger('transaction_type_id')->nullable();
            $table->decimal('reward_points_earned');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->date('reward_approved_date')->nullable();
            $table->date('reward_point_expire_date')->nullable();
            $table->tinyInteger('reward_point_status');
            $table->text('discription')->nullable();
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
        Schema::dropIfExists('trn_customer_rewards');
    }
}
