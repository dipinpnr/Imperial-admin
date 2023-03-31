<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__videos', function (Blueprint $table) {
            $table->bigIncrements('video_id');
            $table->text('platform')->nullable();
            $table->text('video_code')->nullable();
            $table->boolean('status')->nullable();
            $table->integer('visibility')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('town_id')->nullable();
            $table->string('video_image', 250)->nullable();
            $table->text('video_discription')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('mst__videos');
    }
}
