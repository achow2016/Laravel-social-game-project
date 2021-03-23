<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameEnemiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('game_enemies');
        Schema::create('game_enemies', function (Blueprint $table) {
            $table->increments('id');
			$table->binary('avatar')->nullable();
			$table->string('gameRace');
			$table->string('name');
			$table->string('gameClass');
			$table->string('weapon')->unsigned()->nullable();
			$table->string('offHand')->unsigned()->nullable();
			$table->string('bodyEquipment')->unsigned()->nullable();
			$table->string('headEquipment')->unsigned()->nullable();
			$table->string('armsEquipment')->unsigned()->nullable();
			$table->string('legsEquipment')->unsigned()->nullable();
			$table->integer('gameLevel')->default('0');
			$table->integer('health')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('accuracy')->default('1');
			$table->integer('attack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->integer('staminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('money')->default('0');
            $table->timestamps();
			
			$table->foreign('weapon')->references('name')->on('weapons')->onDelete('cascade'); 
			$table->foreign('offHand')->references('name')->on('offhand_equipment')->onDelete('cascade'); 
			$table->foreign('bodyEquipment')->references('name')->on(body_equipment)->onDelete('cascade'); 
			$table->foreign('headEquipment')->references('name')->on('head_equipment')->onDelete('cascade'); 
			$table->foreign('armsEquipment')->references('name')->on('arms_equipment')->onDelete('cascade'); 
			$table->foreign('legsEquipment')->references('name')->on('legs_equipment')->onDelete('cascade'); 
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_enemies');
    }
}
