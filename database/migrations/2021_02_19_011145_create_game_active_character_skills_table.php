<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameActiveCharacterSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_active_character_skills', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('characterId')->unsigned();
			$table->integer('skillId')->unsigned();
			$table->string('name');
			$table->string('bodyTarget');
			$table->string('stanceResult');
			$table->string('debuff');
			$table->integer('debuffPercent');
			$table->integer('debuffDuration');
			$table->integer('debuffStackQuantity');
			$table->integer('debuffStackMax');
			$table->string('effect');
			$table->integer('effectQuantity');
			$table->integer('range');
			$table->integer('accuracyPercent');
			$table->integer('meleePenaltyPercent');
			$table->integer('staminaCost');
			$table->timestamps();
			$table->foreign('characterId')->references('id')->on('character')->onDelete('cascade');			
			$table->foreign('skillId')->references('id')->on('game_skills')->onDelete('cascade');	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_active_character_skills');
    }
}
