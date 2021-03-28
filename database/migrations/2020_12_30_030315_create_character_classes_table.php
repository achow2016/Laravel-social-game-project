<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacterClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('character_classes', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name')->unique();
			$table->json('weaknesses')->nullable();
			$table->json('resistances')->nullable();
			$table->json('skills')->nullable();
			$table->integer('health')->default('0');
			$table->integer('stamina')->default('0');
			$table->integer('accuracy')->default('100');
			$table->integer('attack')->default('0');
			$table->integer('baseAttackCost')->default('0');
			$table->integer('staminaRegen')->default('0');
			$table->integer('healthRegen')->default('0');
			$table->integer('agility')->default('0');
			$table->integer('defense')->default('0');
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
        Schema::dropIfExists('character_classes');
    }
}
