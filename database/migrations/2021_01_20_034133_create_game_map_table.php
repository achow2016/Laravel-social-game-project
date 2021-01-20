<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameMapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_map', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('character_id')->unsigned();
			$table->string('name');
			$table->binary('tileMain')->nullable();
			$table->json('startPoint');
			$table->json('endPoint');
            $table->timestamps();
			$table->foreign('character_id')->references('id')->on('character')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_map');
    }
}
