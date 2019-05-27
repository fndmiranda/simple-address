<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressNeighborhoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_neighborhoods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->uuid('city_id');
            $table->foreign('city_id')->references('id')->on('address_cities')->onDelete('cascade');
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
        Schema::dropIfExists('address_neighborhoods');
    }
}
