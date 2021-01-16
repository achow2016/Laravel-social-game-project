<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashshopInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('cashshop_inventory');
        Schema::create('cashshop_inventory', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name');
			$table->longText('itemDescription');
			$table->string('type');
			$table->integer('duration')->nullable();
			$table->integer('quantity')->default('0');
			$table->integer('cost')->default('0');
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
        Schema::dropIfExists('cashshop_inventory');
    }
}
