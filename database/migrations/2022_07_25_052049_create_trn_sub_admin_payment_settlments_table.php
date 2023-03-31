<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnSubAdminPaymentSettlmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_sub_admin_payment_settlments', function (Blueprint $table) {
            $table->bigIncrements('sub_admin_payment_settlments_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('subadmin_id');
            $table->string('commision_percentage', 191);
            $table->string('commision_amount', 191);
            $table->string('sub_admin_commision', 191);
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
        Schema::dropIfExists('trn_sub_admin_payment_settlments');
    }
}
