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
			$table->boolean('battleTurnMade')->default(false);
			$table->string('name');
			$table->binary('avatar')->nullable();
			$table->binary('meleeAnimation')->nullable();
			$table->integer('turnPosition')->nullable();
			$table->json('turnAction')->nullable();
			$table->json('effects')->nullable();
			$table->json('visibleTiles')->nullable();
			$table->json('stance')->nullable();
			$table->json('mapPosition')->nullable();
			$table->integer('mapId');
			//$table->integer('mapId')->unsigned();
			$table->string('race');
			$table->string('class');
			$table->integer('armour')->default('0');
			$table->integer('health')->default('0');
			$table->integer('combatRange')->default('1');
			$table->integer('sightRange')->nullable();
			$table->integer('currentHealth')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('currentStamina')->default('0');
			$table->integer('accuracy')->default('100');
			$table->integer('currentAccuracy')->default('100');
			$table->integer('defense')->default('0');
			$table->integer('currentDefense')->default('0');
			$table->integer('attack')->default('0');
			$table->integer('currentAttack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->string('weapon')->nullable();
			$table->string('offHand')->nullable();
			$table->string('bodyEquipment')->nullable();
			$table->string('headEquipment')->nullable();
			$table->string('armsEquipment')->nullable();
			$table->string('legsEquipment')->nullable();
			$table->integer('staminaRegen')->default('0');
			$table->integer('currentStaminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('currentHealthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('currentAgility')->default('0');
			$table->integer('money')->default('0');
			$table->integer('attackMultiplier')->default('1');
			$table->integer('defenseMultiplier')->default('1');
            $table->timestamps();
		
			//$table->foreign('mapId')->references('id')->on('game_maps')->onDelete('cascade'); 
			$table->foreign('race')->references('race')->on('character_races')->onDelete('cascade'); 
			$table->foreign('class')->references('class')->on('character_classes')->onDelete('cascade');
			
			$table->foreign('weapon')->references('name')->on('weapons')->onDelete('cascade'); 
			$table->foreign('offHand')->references('name')->on('offhand_equipment')->onDelete('cascade'); 
			$table->foreign('bodyEquipment')->references('name')->on('body_equipment')->onDelete('cascade'); 
			$table->foreign('headEquipment')->references('name')->on('head_equipment')->onDelete('cascade'); 
			$table->foreign('armsEquipment')->references('name')->on('arms_equipment')->onDelete('cascade'); 
			$table->foreign('legsEquipment')->references('name')->on('legs_equipment')->onDelete('cascade'); 
			
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
