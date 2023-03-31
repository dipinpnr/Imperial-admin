<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnPointsRedeemedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn_points_redeemeds', function (Blueprint $table) {
            $table->bigIncrements('points_redeemed_id');
            $table->integer('points')->nullable();
            $table->boolean('isActive')->nullable()->default(false);
            $table->timestamps();
            $table->bigInteger('customer_id')->nullable();
            $table->string('discription', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn_points_redeemeds');
    }
}
