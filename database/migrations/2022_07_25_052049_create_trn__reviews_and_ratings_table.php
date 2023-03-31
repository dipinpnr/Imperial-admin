<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnReviewsAndRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__reviews_and_ratings', function (Blueprint $table) {
            $table->bigIncrements('reviews_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable();
            $table->decimal('rating', 4)->nullable();
            $table->text('review')->nullable();
            $table->timestamp('reviews_date')->nullable();
            $table->timestamps();
            $table->tinyInteger('isVisible')->nullable()->default(0);
            $table->softDeletes('deleted_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trn__reviews_and_ratings');
    }
}
