<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_skills', function (Blueprint $table) {
			$table->increments('id');
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_skills');
    }
}
