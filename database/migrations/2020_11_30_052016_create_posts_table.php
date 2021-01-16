<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('posts');
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('post_id');
			$table->integer('user_id')->unsigned();
            $table->string('name');
            $table->longText('postText');
            $table->date('date');
            $table->timestamps();
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
        Schema::dropIfExists('posts');
    }
}
