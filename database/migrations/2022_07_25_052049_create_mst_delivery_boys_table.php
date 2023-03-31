<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstDeliveryBoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_delivery_boys', function (Blueprint $table) {
            $table->bigIncrements('delivery_boy_id');
            $table->string('delivery_boy_name', 191);
            $table->string('delivery_boy_mobile', 191);
            $table->string('delivery_boy_email', 191)->nullable();
            $table->string('delivery_boy_image', 191)->nullable();
            $table->longText('delivery_boy_address');
            $table->string('vehicle_number', 191);
            $table->unsignedBigInteger('country_id')->index('mst_delivery_boys_country_id_foreign');
            $table->unsignedBigInteger('state_id')->index('mst_delivery_boys_state_id_foreign');
            $table->unsignedBigInteger('district_id')->index('mst_delivery_boys_district_id_foreign');
            $table->bigInteger('town_id')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->index('mst_delivery_boys_vehicle_type_id_foreign');
            $table->string('delivery_boy_availability_id', 100)->nullable()->index('mst_delivery_boys_delivery_boy_availability_id_foreign');
            $table->unsignedBigInteger('store_id')->nullable()->index('mst_delivery_boys_store_id_foreign');
            $table->bigInteger('subadmin_id')->nullable();
            $table->decimal('delivery_boy_commision')->nullable();
            $table->string('delivery_boy_commision_amount', 191);
            $table->string('delivery_boy_username', 191)->unique();
            $table->string('password', 191);
            $table->tinyInteger('delivery_boy_status');
            $table->smallInteger('availability_status')->nullable()->default(1);
            $table->string('latitude', 100)->nullable();
            $table->string('longitude', 100)->nullable();
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
        Schema::dropIfExists('mst_delivery_boys');
    }
}
