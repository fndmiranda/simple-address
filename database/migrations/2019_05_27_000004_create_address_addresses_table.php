<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->integer('postcode');
            $table->uuid('neighborhood_id');
            $table->foreign('neighborhood_id')->references('id')->on('address_neighborhoods')->onDelete('cascade');
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
        Schema::dropIfExists('address_addresses');
    }
}
