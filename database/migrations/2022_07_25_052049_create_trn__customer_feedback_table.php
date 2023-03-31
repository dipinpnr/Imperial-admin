<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnCustomerFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__customer_feedback', function (Blueprint $table) {
            $table->bigIncrements('feedback_id');
            $table->bigInteger('feedback_question_id')->nullable()->default(0);
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable();
            $table->string('feedback', 191)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__customer_feedback');
    }
}
