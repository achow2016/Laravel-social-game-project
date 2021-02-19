<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameActiveCharacterSkillsetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_active_character_skillsets', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('characterId')->unsigned();		
			$table->foreign('characterId')->references('id')->on('character')->onDelete('cascade');			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_active_character_skillsets');
    }
}
