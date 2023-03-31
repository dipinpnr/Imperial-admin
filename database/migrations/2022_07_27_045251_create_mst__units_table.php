<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst__units', function (Blueprint $table) {
            $table->integer('unit_id', true);
            $table->string('unit_name', 100);
            $table->string('unit_sf', 20);
            $table->boolean('is_active');
            $table->boolean('is_deleted')->nullable();
            $table->softDeletes('deleted_at', 6);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst__units');
    }
}
