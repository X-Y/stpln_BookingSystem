<?php

use Illuminate\Database\Migrations\Migration;

class AddSimplePermission extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::table("users",function($table){
			$table->integer("role");
		
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
		Schema::table("users",function($table){
			$table->dropColumn("role");
		});
	}

}