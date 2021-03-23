<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameActiveEnemySkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_active_enemy_skills', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('ownerId')->unsigned();
			$table->integer('skillId')->unsigned();
			$table->string('name');
			$table->json('stance')->nullable();
			$table->json('bodyTarget')->nullable();
			$table->string('debuff');
			$table->string('debuffEffectPercentage');
			$table->string('debuffDuration');
			$table->string('buff');
			$table->string('buffEffectPercentage');
			$table->string('buffDuration');
			$table->integer('range');
			$table->integer('staminaCost');
			$table->decimal('effectChance', 3, 2);
			$table->decimal('damagePenalty', 3, 2);
            $table->timestamps();
			$table->foreign('ownerId')->references('id')->on('game_active_enemies')->onDelete('cascade');			
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
        Schema::dropIfExists('game_active_enemy_skills');
    }
}
