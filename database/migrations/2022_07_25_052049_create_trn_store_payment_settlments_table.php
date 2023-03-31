<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentSettlmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_payment_settlments', function (Blueprint $table) {
            $table->integer('settlment_id', true);
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('store_id');
            $table->decimal('store_commision_amount');
            $table->decimal('admin_commision_amount');
            $table->decimal('total_amount');
            $table->decimal('commision_paid')->nullable();
            $table->decimal('commision_to_be_paid')->nullable();
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
        Schema::dropIfExists('trn_store_payment_settlments');
    }
}
