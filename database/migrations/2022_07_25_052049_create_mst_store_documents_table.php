<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstStoreDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_store_documents', function (Blueprint $table) {
            $table->bigIncrements('store_document_id');
            $table->string('store_document_license', 100)->nullable();
            $table->string('store_document_gstin', 191)->nullable();
            $table->string('store_document_file_head', 291)->nullable();
            $table->string('store_document_other_file', 191)->nullable();
            $table->unsignedBigInteger('store_id')->index('mst_store_documents_store_id_foreign');
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
        Schema::dropIfExists('mst_store_documents');
    }
}
