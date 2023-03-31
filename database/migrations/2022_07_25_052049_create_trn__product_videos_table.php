<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnProductVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__product_videos', function (Blueprint $table) {
            $table->bigIncrements('product_video_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_varient_id')->nullable();
            $table->text('link')->nullable();
            $table->string('platform', 191)->nullable();
            $table->tinyInteger('is_active')->nullable();
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
        Schema::dropIfExists('trn__product_videos');
    }
}
