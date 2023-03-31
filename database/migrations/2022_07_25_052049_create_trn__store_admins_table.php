<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrnStoreAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trn__store_admins', function (Blueprint $table) {
            $table->bigIncrements('store_admin_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('admin_name', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('username', 191)->nullable();
            $table->string('store_mobile', 191)->nullable();
            $table->bigInteger('role_id')->nullable();
            $table->boolean('store_account_status')->nullable();
            $table->string('store_otp_verify_status', 10)->default('0');
            $table->date('expiry_date')->nullable();
            $table->string('password', 191)->nullable();
            $table->bigInteger('subadmin_id')->nullable();
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
        Schema::dropIfExists('trn__store_admins');
    }
}
