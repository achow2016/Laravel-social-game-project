<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('replies');
        Schema::create('replies', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->integer('target_post_id')->unsigned();
            $table->string('name');
            $table->longText('postText');
            $table->date('date');
            $table->timestamps();
			$table->foreign('target_post_id')->references('post_id')->on('posts')->onDelete('cascade'); 
			$table->foreign('user_id')->references('id')->on('rpggameusers')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replies');
    }
}
