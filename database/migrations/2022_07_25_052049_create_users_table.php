<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 191);
            $table->string('email', 191)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 191);
            $table->tinyInteger('user_role_id');
            $table->tinyInteger('added_by');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->string('admin_name', 45)->nullable();
            $table->string('phone_number', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
