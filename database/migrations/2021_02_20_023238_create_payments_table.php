<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
			$table->decimal('amount', $precision = 12, $scale = 2); //precision.scale\
			$table->string('email');
			$table->string('name');
			$table->integer('userId')->unsigned();
			$table->integer('orderId')->unsigned();
			$table->foreign('userId')->references('id')->on('rpggameusers')->onDelete('cascade'); 
			$table->foreign('orderId')->references('id')->on('orders')->onDelete('cascade'); 
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
        Schema::dropIfExists('payments');
    }
}
