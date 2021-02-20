<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('game_maps');
        Schema::create('game_maps', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('level')->default('0');
			$table->json('startPoint')->nullable();
			$table->json('playerPosition')->nullable();
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
        Schema::dropIfExists('game_maps');
    }
}
