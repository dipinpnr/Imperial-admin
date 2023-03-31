<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCustomerRewardTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_customer_reward_transaction_types', function (Blueprint $table) {
            $table->integer('transaction_type_id', true);
            $table->string('transaction_type', 100);
            $table->string('transaction_rule', 100);
            $table->decimal('transaction_point_value');
            $table->decimal('transaction_earning_point');
            $table->decimal('min_purchase_amount');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_customer_reward_transaction_types');
    }
}
