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
        Schema::create('game_active_enemies', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name');
			$table->binary('avatar')->nullable();
			$table->binary('meleeAnimation')->nullable();
			$table->integer('mapId')->unsigned();
			$table->integer('raceId')->unsigned();
			$table->integer('classId')->unsigned();
			$table->integer('health')->default('0');
			$table->integer('currentHealth')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('currentStamina')->default('0');
			$table->integer('accuracy')->default('1');
			$table->integer('currentAccuracy')->default('1');
			$table->integer('attack')->default('0');
			$table->integer('currentAttack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->json('skills')->nullable();
			$table->json('itemLootInventory')->nullable();
			$table->json('mapPosition')->nullable();
			$table->integer('staminaRegen')->default('0');
			$table->integer('currentStaminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('currentHealthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('currentAgility')->default('0');
			$table->integer('money')->default('0');
			$table->integer('attackMulitplier')->default('1');
			$table->integer('defenseMulitplier')->default('1');
            $table->timestamps();
			
			//$table->integer('skillSet')->unsigned();
			//$table->integer('inventorySet')->unsigned();
            
			
			$table->foreign('mapId')->references('id')->on('game_maps')->onDelete('cascade'); 
			$table->foreign('raceId')->references('id')->on('character_races')->onDelete('cascade'); 
			$table->foreign('classId')->references('id')->on('character_classes')->onDelete('cascade'); 
			
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
