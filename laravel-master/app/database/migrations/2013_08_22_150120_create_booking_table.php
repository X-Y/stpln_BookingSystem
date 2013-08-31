<?php

use Illuminate\Database\Migrations\Migration;

class CreateBookingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("bookings",function($table){
			$table->increments("id");
			$table->string("user");
			$table->string("title",100);
			$table->text("note")->nullable();
			$table->dateTime("from")->index();
			$table->dateTime("to");
			$table->integer("status")->default(1);
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
		Schema::drop("bookings");
	}

}