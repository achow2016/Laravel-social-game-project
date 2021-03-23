<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBodyEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('body_equipment', function (Blueprint $table) {
            $table->increments('id');
			$table->string('name');
			$table->binary('image')->nullable();
			$table->integer('gameLevel')->default('1');
			$table->longText('description')->nullable();
			$table->integer('attack')->default('0');
			$table->integer('armour')->default('0');
			$table->integer('defense')->default('0');
			$table->integer('cost')->default('0');
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
        Schema::dropIfExists('body_equipment');
    }
}
