<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnOrderInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_order_invoices', function (Blueprint $table) {
            $table->bigIncrements('order_invoice_id');
            $table->unsignedBigInteger('order_id');
            $table->date('invoice_date');
            $table->string('invoice_id', 191);
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
        Schema::dropIfExists('trn_order_invoices');
    }
}
