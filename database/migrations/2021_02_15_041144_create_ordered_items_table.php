<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_items', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('order_id')->unsigned();
			$table->integer('item_id')->unsigned();
			$table->string('name');
			$table->longText('itemDescription');
			$table->string('type');
			$table->integer('duration')->nullable();
			$table->integer('quantity')->default('0');
			$table->integer('cost')->default('0');
            $table->timestamps();
			$table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade'); 
			$table->foreign('item_id')->references('id')->on('cashshop_inventory')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordered_items');
    }
}
