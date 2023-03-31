<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStorePaymentsTrackerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_store_payments_tracker', function (Blueprint $table) {
            $table->bigIncrements('store_payments_tracker_id');
            $table->unsignedBigInteger('store_id');
            $table->integer('commision_paid');
            $table->text('payment_note')->nullable();
            $table->date('date_of_payment');
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
        Schema::dropIfExists('trn_store_payments_tracker');
    }
}
