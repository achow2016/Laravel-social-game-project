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
			$table->string('weapon')->nullable();
			$table->string('offHand')->nullable();
			$table->string('bodyEquipment')->nullable();
			$table->string('headEquipment')->nullable();
			$table->string('armsEquipment')->nullable();
			$table->string('legsEquipment')->nullable();
			$table->integer('gameLevel')->default('0');
			$table->integer('money')->default('0');
			$table->json('itemLootInventory')->nullable();
			$table->json('skills')->nullable();
            $table->timestamps();
			
			$table->foreign('weapon')->references('name')->on('weapons')->onDelete('cascade'); 
			$table->foreign('offHand')->references('name')->on('offhand_equipment')->onDelete('cascade'); 
			$table->foreign('bodyEquipment')->references('name')->on('body_equipment')->onDelete('cascade'); 
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
