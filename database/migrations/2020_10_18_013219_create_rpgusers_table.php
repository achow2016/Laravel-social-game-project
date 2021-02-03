<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpgusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	 
	protected $casts = [
        //'saveGame' => 'array'
    ];
	
    public function up()
    {
        Schema::create('rpggameusers', function (Blueprint $table) {
            $table->increments('id');
			$table->binary('avatar')->nullable();
			$table->binary('profile_video')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('account_verified_date')->nullable();
            $table->string('password');
			$table->integer('credits')->default(0);
			$table->boolean('membership')->default(false);
			$table->date('membership_start_date')->nullable();
			$table->date('membership_end_date')->nullable();
			$table->integer('playtime')->default(0);
			//$table->json('saveGame')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('rpggameusers');
    }
}
