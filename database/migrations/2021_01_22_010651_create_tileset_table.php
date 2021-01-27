<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTilesetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tileset', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('map_id')->unsigned();
			$table->json('mapData')->nullable();
			$table->decimal('grassCover', $precision = 3, $scale = 2);
			$table->decimal('waterCover', $precision = 3, $scale = 2);
			$table->decimal('treeCover', $precision = 3, $scale = 2);
            $table->timestamps();
			$table->foreign('map_id')->references('character_id')->on('game_map')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tileset');
    }
}
