<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameActiveEnemiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('game_active_enemies');
        Schema::create('game_active_enemies', function (Blueprint $table) {
            $table->increments('id');
			$table->binary('avatar')->nullable();
			$table->string('gameRace');
			$table->string('name');
			$table->string('gameClass');
			$table->integer('ownerMap')->unsigned();
			$table->integer('health')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('accuracy')->default('1');
			$table->integer('attack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->json('skills')->nullable();
			$table->json('itemLootInventory')->nullable();
			$table->json('mapPosition')->nullable();
			$table->integer('staminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('money')->default('0');
			$table->integer('attackMulitplier')->default('1');
			$table->integer('defenseMulitplier')->default('1');
            $table->timestamps();
			
			//$table->integer('skillSet')->unsigned();
			//$table->integer('inventorySet')->unsigned();
            
			
			$table->foreign('ownerMap')->references('id')->on('game_map')->onDelete('cascade'); 
			
			//$table->foreign('skillSet')->references('id')->on('skill_sets')->onDelete('cascade'); 
			//$table->foreign('inventorySet')->references('id')->on('inventory_sets')->onDelete('cascade'); 
	 });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_active_enemies');
    }
}
