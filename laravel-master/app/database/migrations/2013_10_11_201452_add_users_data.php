<?php

use Illuminate\Database\Migrations\Migration;

class AddUsersData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table('users',function($table){
			$table->integer('credits');
			$table->string('phone number');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::table('users',function($table){
			$table->dropColumn('credits');
			$table->dropColumn('phone');
		});
	}

}