<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnGlobalProductVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__global_product_videos', function (Blueprint $table) {
            $table->bigIncrements('global_product_video_id');
            $table->unsignedBigInteger('global_product_id')->default(0);
            $table->text('platform')->nullable();
            $table->text('video_code')->nullable();
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
        Schema::dropIfExists('trn__global_product_videos');
    }
}
