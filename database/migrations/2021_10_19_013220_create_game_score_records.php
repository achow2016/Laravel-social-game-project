<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameScoreRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_score_records', function (Blueprint $table) {
            $table->increments('id');
			$table->binary('avatar')->nullable();
			$table->integer('gameLevel');
			$table->json('mapData')->nullable();
			$table->json('playerMapPosition')->nullable();
			$table->json('enemyMapPositions')->nullable();
			$table->string('race');
			$table->string('class');
			$table->integer('userId')->unsigned();
			$table->string('userName');
			$table->string('characterName')->unique();
			$table->integer('health');
			$table->integer('stamina');
			$table->integer('accuracy');
			$table->integer('defense');
			$table->integer('attack');
			$table->integer('armour');
			$table->integer('staminaRegen');
			$table->integer('healthRegen');
			$table->integer('agility');
			$table->integer('damageDealt');
			$table->integer('damageReceived');
			$table->integer('itemsUsed');
			$table->integer('enemiesKilled');
			$table->integer('squaresMoved');
			$table->integer('money');
			$table->integer('totalEarnings');
			$table->integer('score');
			$table->string('weapon')->nullable();
			$table->string('offHandEquipment')->nullable();
			$table->string('bodyEquipment')->nullable();
			$table->string('headEquipment')->nullable();
			$table->string('armsEquipment')->nullable();
			$table->string('legsEquipment')->nullable();
			$table->timestamps();
			//$table->foreign('user')->references('name')->on('rpggameusers')->onDelete('cascade');
		});
		
		Schema::table('game_score_records', function($table) {
			$table->foreign('userId')->references('id')->on('rpggameusers')->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_score_records');
    }
}
