<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorGuestbookNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_guestbook_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_id')->unsigned();
            $table->longText('note');
			$table->string('email')->nullable();
			$table->string('name');
			$table->string('country')->nullable();
			$table->date('date');
            $table->timestamps();
			$table->foreign('visitor_id')->references('id')->on('visitor_records')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitor_guestbook_notes');
    }
}
