<?php

use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("booking_rules",function($table){
			$table->increments("id");
			$table->string("name");
			$table->smallInteger("frequency")->index();
			$table->boolean("action");
			$table->string("from");
			$table->string("to");
			$table->boolean("active")->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("booking_rules");
	}

}