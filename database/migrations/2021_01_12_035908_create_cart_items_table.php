<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('cart_items');
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('cart_id')->unsigned();
			$table->string('name');
			$table->integer('quantity');
			$table->integer('price');
            $table->timestamps();
			$table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
