<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressAddressablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_addressables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('address_id');
            $table->foreign('address_id')->references('id')->on('address_addresses')->onDelete('cascade');
            $table->{config('address.column_type')}('addressable_id');
            $table->string('addressable_type');
            $table->integer('number')->nullable();
            $table->string('complement', 150)->nullable();
            $table->decimal('lat', 11, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
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
        Schema::dropIfExists('address_addressables');
    }
}
