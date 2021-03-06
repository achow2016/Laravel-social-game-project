<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameActiveEnemyItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_active_enemy_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('itemId')->unsigned();
			$table->integer('ownerId')->unsigned();
			$table->integer('quantity');
			$table->timestamps();
			$table->foreign('itemId')->references('id')->on('game_items')->onDelete('cascade');			
			$table->foreign('ownerId')->references('id')->on('game_active_enemies')->onDelete('cascade');	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_active_enemy_items');
    }
}
