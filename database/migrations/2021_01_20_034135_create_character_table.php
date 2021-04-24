<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('character', function (Blueprint $table) {
            $table->increments('id');
			//battle state
            $table->boolean('battle')->default(false);
			$table->integer('gameTurns')->nullable();
			$table->integer('currentTurn')->nullable(); 
			$table->integer('turnPosition')->nullable();
            $table->integer('enemyId')->unsigned()->nullable();
			$table->integer('engageDistance')->nullable();
			$table->integer('combatRange')->default('1');
			$table->binary('avatar')->nullable();
			$table->binary('meleeAnimation')->nullable();
			$table->integer('mapId')->unsigned()->nullable();
			$table->integer('gameLevel')->default('1');
			$table->json('mapPosition')->nullable();
			$table->json('stance')->nullable();
			$table->integer('raceId')->unsigned();
			$table->integer('classId')->unsigned();
			$table->integer('ownerUser')->unsigned();
			$table->string('characterName')->unique();
			$table->integer('health')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('currentHealth')->default('0');
			$table->integer('currentStamina')->default('0');
			$table->integer('accuracy')->default('100');
			$table->integer('currentAccuracy')->default('100');
			$table->integer('defense')->default('0');
			$table->integer('currentDefense')->default('0');
			$table->integer('attack')->default('0');
			$table->integer('currentAttack')->default('0');
			$table->integer('armour')->default('0');
			$table->integer('staminaRegen')->default('0');
			$table->integer('currentStaminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('currentHealthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('currentAgility')->default('0');
			$table->integer('attackMultiplier')->default('1');
			$table->integer('defenseMultiplier')->default('1');
			$table->integer('baseAttackCost')->default('0');
			$table->integer('damageDealt')->default('0');
			$table->integer('damageReceived')->default('0');
			$table->integer('itemsUsed')->default('0');
			$table->integer('enemiesKilled')->default('0');
			$table->integer('squaresMoved')->default('0');
			$table->integer('money')->default('0');
			$table->integer('totalEarnings')->default('0');
			$table->integer('score')->default('0');
			$table->string('weapon')->nullable();
			$table->string('offHand')->nullable();
			$table->string('bodyEquipment')->nullable();
			$table->string('headEquipment')->nullable();
			$table->string('armsEquipment')->nullable();
			$table->string('legsEquipment')->nullable();
			//$table->integer('skillSet')->unsigned();
			//$table->integer('inventorySet')->unsigned();
            
			$table->timestamps();
			$table->foreign('ownerUser')->references('id')->on('rpggameusers')->onDelete('cascade'); 
			$table->foreign('raceId')->references('id')->on('character_races')->onDelete('cascade'); 
			$table->foreign('classId')->references('id')->on('character_classes')->onDelete('cascade'); 
			$table->foreign('mapId')->references('id')->on('game_maps')->onDelete('cascade'); 
			$table->foreign('enemyId')->references('id')->on('game_active_enemies')->onDelete('cascade');
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
        Schema::dropIfExists('character');
    }
}
