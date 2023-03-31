<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnConfigurePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_configure_points', function (Blueprint $table) {
            $table->bigIncrements('configure_points_id');
            $table->integer('registraion_points')->nullable();
            $table->integer('first_order_points')->nullable();
            $table->integer('referal_points')->nullable();
            $table->integer('joiner_points')->nullable();
            $table->integer('rupee');
            $table->integer('rupee_points')->nullable();
            $table->integer('order_amount');
            $table->integer('order_points');
            $table->integer('redeem_percentage')->nullable();
            $table->integer('max_redeem_amount')->nullable();
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
        Schema::dropIfExists('trn_configure_points');
    }
}
