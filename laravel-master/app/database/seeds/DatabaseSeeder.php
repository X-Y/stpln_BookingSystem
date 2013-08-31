<?php
/*
*
*	frequency
*	0: one-time rule
*	1: daily
*	2: weekly
*	3: monthly
*	4: yearly
*
*	action
*	true: allow
*	false: disallow
*
*/
class DatabaseSeeder extends Seeder{
	public function run(){
		Eloquent::unguard();
		$this->call('BookingRulesSeeder');
		$this->command->info("fuck!");
	}
}

class BookingRulesSeeder extends Seeder{
	public function run(){
		DB::table("booking_rules")->delete();
		BookingRule::create(array(
			'name'=>'daily opening hours',
			'frequency'=>'1',
			'action'=>true,
			'from'=>'PT9H',
			'to'=>'PT22H',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'daily lunch hours',
			'frequency'=>'1',
			'action'=>false,
			'from'=>'PT12H',
			'to'=>'PT13H',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'weekly hacker night',
			'frequency'=>'2',
			'action'=>false,
			'from'=>'P4DT18H',
			'to'=>'P4DT22H',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'Special day off',
			'frequency'=>'0',
			'action'=>false,
			'from'=>'P2013Y9M10D',
			'to'=>'P2013Y9M11D',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'Weekend off',
			'frequency'=>'2',
			'action'=>false,
			'from'=>'P6D',
			'to'=>'P7D',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'Monthly mantance',
			'frequency'=>'3',
			'action'=>false,
			'from'=>'P6D',
			'to'=>'P7D',
			'active'=>true
		));
		BookingRule::create(array(
			'name'=>'Christmas holiday',
			'frequency'=>'4',
			'action'=>false,
			'from'=>'P12M25D',
			'to'=>'P12M31D',
			'active'=>true
		));

	}
}