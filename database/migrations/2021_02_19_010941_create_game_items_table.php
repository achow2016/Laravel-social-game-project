<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_items', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->string('description');
			$table->string('effect');
			$table->integer('effectStackAmount');
			$table->integer('effectStackLimit');
			$table->integer('effectPercent');
			$table->integer('effectDuration');
			$table->integer('shopValue');
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
        Schema::dropIfExists('game_items');
    }
}
