<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstRewardToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__reward_to_customers', function (Blueprint $table) {
            $table->bigIncrements('reward_to_customer_id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('customer_mobile_number', 191)->nullable();
            $table->text('reward_discription')->nullable();
            $table->integer('reward_points')->default(0);
            $table->boolean('reward_status')->default(false);
            $table->date('added_date')->nullable();
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
        Schema::dropIfExists('mst__reward_to_customers');
    }
}
