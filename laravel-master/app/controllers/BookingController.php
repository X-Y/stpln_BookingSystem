<?php
class BookingController extends BaseController{
	public function getIndex(){
	
		return View::make("booking");
	}
	public function postIndex(){
		$booking=new Booking;
		$dts=self::calDates($_POST);
		return var_dump(BookingCheck::isBookingAllowed($dts["from"],$dts["to"]));
		//if(!self::isBookingAllowed($dts["from"],$dts["to"]))return "fail";
		
		$booking["title"]=$_POST["title"];
		$booking["user"]=12334;
		$booking["from"]=$dts["from"];
		$booking["to"]=$dts["to"];	
		
		$succ=$booking->save();
		if($succ){
			return "success!";
		}else{
			return var_dump($booking->errors()->all());
		}
	}
	
	
	private static function calDates($package){
		$date_from=new DateTime($package["date"]);
		$date_to=clone($date_from);
		$time_from=explode(":",$package["from"]);
		$time_to=explode(":",$package["to"]);
		$date_from->setTime($time_from[0],$time_from[1]);
		$date_to->setTime($time_to[0],$time_to[1]);
		
		return [
				"from"	=>	$date_from,
				"to"	=>	$date_to
				];
	}
	
}

class BookingCheck{
	public static function isBookingAllowed($from,$to){
		if(self::isRuleAllowed($from,$to) && self::isTimeFree($from,$to)){
			return true;
		}else{
			return false;
		}
	}

	private static function actionCheck($from,$to,$from_rule,$to_rule,$action){
		if($action){
			if($from<$from_rule || $to>$to_rule){
				return false;
			}else{
				return true;
			}
		}else{
			if(($from>=$from_rule && $from<$to_rule) || ($to>$from_rule && $to<=$to_rule)){
				return false;
			}else{
				return true;
			}
		}
	}
	private static function checkOneTimeRule($from,$to,$fromInte,$toInte){
		$beginOfTime=new DateTime("0000-01-01 0:0:0");
		return $beginOfTime;
	}
	private static function checkDailyRule($from,$to,$fromInte,$toInte){
		$beginOftheDay=new DateTime($from->format("Y-m-d 0:0:0"));
		return $beginOftheDay;
	}
	private static function checkWeeklyRule($from,$to,$fromInte,$toInte){
		$temp=strtotime("last Monday",$from->getTimestamp());
		$beginOftheWeek=(new DateTime())->setTimeStamp($temp);
		return $beginOftheWeek;
	}
	private static function checkMonthlyRule($from,$to,$fromInte,$toInte){
		$beginOfMonth=new DateTime($from->format("Y-m-1 0:0:0"));
		return $beginOfMonth;
	}
	private static function checkYearlyRule($from,$to,$fromInte,$toInte){
		$beginOfYear=new DateTime($from->format("Y-1-1 0:0:0"));
		return $beginOfYear;
	}
	private static function getRulePeriods($initial,$fromInte,$toInte){
		$from_rule=clone $initial;
		$from_rule_date=new DateInterval($fromInte);
		if($from_rule_date->d>0)$from_rule_date->d--;
		if($from_rule_date->m>0)$from_rule_date->m--;
		$from_rule->add($from_rule_date);
		
		$to_rule=clone $initial;
		$to_rule_date=new DateInterval($toInte);
		if($to_rule_date->d>0)$to_rule_date->d--;
		if($to_rule_date->m>0)$to_rule_date->m--;
		$to_rule->add($to_rule_date);
		
		return [$from_rule,$to_rule];
	}
	private static function isRuleAllowed($from,$to){
		$ruleSets=array(
			0=>"checkOneTimeRule",
			1=>"checkDailyRule",
			2=>"checkWeeklyRule",
			3=>"checkMonthlyRule",
			4=>"checkYearlyRule",
		);
		
		//rules should be gone through until one fails/all passes
		$rules=BookingRule::all();
		foreach($rules as $rule){
			$fromInte=$rule["from"];
			$toInte=$rule["to"];
			$action=$rule['action'];
			
			//get the from and to rule time
			$initial=self::$ruleSets[$rule["frequency"]]($from,$to,$fromInte,$toInte);
			
			$res=self::getRulePeriods($initial,$fromInte,$toInte);
			$from_rule=$res[0];
			$to_rule=$res[1];
			
			//If the booking is following the rule with the corresponding action, it passes
			$pass=self::actionCheck($from,$to,$from_rule,$to_rule,$action);
			
			//If one rule is broken, it's not allowed
			if(!$pass)return false;
		}
		return true;
	}
	
	private static function isTimeFree($from,$to){
		$nextPass=$prevPass=false;
		
		$next=Booking::where("from",">=",$from)->orderBy("from")->first();	//The next one to the desired booking period
		if(is_null($next) || 	//No one next to it? Can book
			$next["from"]>=$to	//End of booking before the next one starts? Can book
			)
			$nextPass=true;	
		
		$prev=Booking::where("from","<=",$from)->orderBy("from","desc")->first(); //The previous one to the desired booking period
		if(is_null($prev) ||	//No one before it? Can book
			$prev["to"]<=$from	//Start booking after the prev one ends? Can book
			)
			$prevPass=true;	
		
		if($nextPass && $prevPass){
			return true;
		}else {
			return false;
		}
	}
	
}