<?php

/*
*
*	BookingCheck
*	Class for doing various check of booking periods
*	
*	isBookingAllowed($from, $to)
*	inquireAvailablePeriods($from,$to)
*	fetchRawPeriods($from,$to)
*
*/
class BookingCheck{
	protected static $ruleSets=array(
			0=>"checkOneTimeRule",
			1=>"checkDailyRule",
			2=>"checkWeeklyRule",
			3=>"checkMonthlyRule",
			4=>"checkYearlyRule",
	);
	
	
	
	/*
	*
	*	public functions 
	*	isBookingAllowed($from, $to)
	*	Checks if the desired period from $from to $to 
	*	is allowed by the rules and not overlapping with other bookings
	*
	*/
	public static function isBookingAllowed($from,$to,$id=Null){
		if(	self::isBasicCheckPassed($from,$to) &&
			self::isRuleAllowed($from,$to) && 
			self::isTimeFree($from,$to,$id)){
			return true;
		}else{
			return false;
		}
	}
	
	/*
	*
	*	inquireAvailablePeriods($from,$to)
	*	Get a list of periods that's available for booking between
	*	$from and $to
	*
	*/
	public static function inquireAvailablePeriods($from,$to,$id=Null){
		if(!self::isBasicCheckPassed($from,$to)){
			return array();
		}
		$raw=self::fetchRawPeriods($from,$to,$id);
		$p_Allowed=$raw["periods_allowed"];
		$p_Disallowed=$raw["periods_disallowed"];
		
		//Just need to care about periods between $from and $to
		$p_toDivide=array(
			array(
			"from"=>$from,
			"to"=>$to
			)
		);
		
		//Get allowed periods first
		$res_all_allowed=self::trimAvailablePeriods($from,$to,$p_Allowed);
		//Remove disallowed periods
		$res=self::makeDividedPeriods($res_all_allowed,$p_Disallowed);
		
		return $res;
	}
	
	/*
	*
	*	fetchRawPeriods($from,$to)
	*	Get all the allowed and disallowed periods between 
	*	$from and $to.
	*
	*	Also a helper function used by makeAvailablePeriods
	*
	*/
	public static function fetchRawPeriods($from,$to,$id=Null){
		$arr_allowed=array();
		$arr_disallowed=array();
		
		//Rule periods
		$rules=BookingRule::active()->get();
		foreach($rules as $rule){
			$action=$rule['action'];
			$res=self::ruleToDT($rule,$from,$to);
			
			if($action){
				array_push($arr_allowed,$res);
			}else{
				array_push($arr_disallowed,$res);
			}
		}
		
		//Booking periods
		$bookings=Booking::active();
		if($id!=Null){
			$bookings=$bookings->except("id");	//ignore itself when modifying a booking
		}
		$bookings=$bookings->where("from",">=",$from)->where("from","<=",$to)->get();
		if(!empty($bookings)){
			foreach($bookings as $booking){
				array_push($arr_disallowed,array("from"=>$booking["from"],"to"=>$booking["to"]));
			}
		}
		
		//Sort the periods from early to late
		function cmp($a,$b){
			return ($a["from"]<$b["from"])?-1:1;
		}
		usort($arr_allowed,"cmp");
		usort($arr_disallowed,"cmp");

		return array(
			"periods_allowed"=>$arr_allowed,
			"periods_disallowed"=>$arr_disallowed
			);
	}	
	
	
	
	
	/*
	*
	*	check if the booking is reasonable ,and within 
	*	BOOKING_ALLOWED_DAYS, BAD. That is counting from
	*	the current moment.
	*
	*/
	private static function isBasicCheckPassed($from,$to){
		if($from == $to){
			return false;
		}
		$from_BAD=new DateTime();
		$diff=new DateInterval("P".Config::get("app.BOOKING_ALLOWED_DAYS")."D");
		
		$to_BAD=clone $from_BAD;
		$to_BAD=$to_BAD->add($diff);
		
		if($from<$from_BAD || $to>$to_BAD){
			return false;
		}else{
			return true;
		}
	}
	
	/*
	*
	*	isRuleAllowed is the enter point of rule engine.
	*	Checks if the booking is allowed according to 
	*	the rule engine.
	*
	*/
	private static function isRuleAllowed($from,$to){
		//rules should be gone through until one fails/all passes
		$rules=BookingRule::active()->orderBy("frequency")->get();
		foreach($rules as $rule){
			$action=$rule['action'];
			$res=self::ruleToDT($rule,$from,$to);
			
			$from_rule=$res["from"];
			$to_rule=$res["to"];
			
			//If the booking is following the rule with the corresponding action, it passes
			$pass=self::actionCheck($from,$to,$from_rule,$to_rule,$action);
			
			//If one rule is broken, it's not allowed
			if(!$pass){
				return false;
			}
		}
		return true;
	}
	/*
	*
	*	isTimeFree checks if the booking period is occupied by other bookings.
	*
	*/
	private static function isTimeFree($from,$to,$id){
		$nextPass=$prevPass=false;
		
		$next=Booking::active();
		if($id!=Null){
			$next=$next->except($id);
		}
		$next=$next->where("from",">=",$from)->orderBy("from")->first();	//The next one to the desired booking period
		if(is_null($next) || 	//No one next to it? Can book
			$next["from"]>=$to	//End of booking before the next one starts? Can book
			)
			$nextPass=true;	
		
		$prev=Booking::active();
		if($id!=Null){
			$prev=$prev->except($id);
		}
		$prev=$prev->where("from","<=",$from)->orderBy("from","desc")->first(); //The previous one to the desired booking period
		
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

	
	
	
	
	
	/*
	*
	*	"action" is either allowed or disallowed in db. So actionCheck is used to
	*	check if booking is valid according to whether it's an allowed or disallowed
	*	rule.
	*
	*/
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
	
	/*
	*
	*	ruleToDT($rule,$from,$to)
	*	It converts $rule to the period of time, using $from, $to 
	*	as reference.
	*
	*	$rule is a record from $rule table, fetched by Eloquent.
	*	$from and $to are the relevant time period
	*/
	private static function ruleToDT($rule,$from,$to){
		$fromInte=$rule["from"];
		$toInte=$rule["to"];
					
		//get the from and to rule time
		$func=self::$ruleSets[$rule["frequency"]];
		$initial=call_user_func_array(array("BookingCheck",$func),array($from,$to,$fromInte,$toInte));
		
		return self::periodsIntevToDT($initial,$fromInte,$toInte);
	}
	/*
	*
	*	checkXXXRule functions define the beginning time of counting,
	*	as rules are all stored as date/time from beginning of the effective
	*	date/time.
	*
	*	e.g. checkDailyRule will apply the hour/minutes to the beginning
	*	of the day, that is Y-m-d 0:0:0
	*
	*/
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
		$now=new DateTime();
		$beginOftheWeek=$now->setTimeStamp($temp);
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
		
	/*
	*	Convert the rule stored in db to DateInterval objects.
	*	Month, day need to be reduced by 1 as they're added to 1
	*	instead of 0. 
	*
	*/
	private static function periodsIntevToDT($initial,$fromInte,$toInte){
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
		
		return array(
			"from"=>$from_rule,
			"to"=>$to_rule
			);
	}
	
	
	
	
	
	
	/*
	*
	*	Helper function used by makeAvailablePeriods, 
	*	limit $p_allowed by $from and $to
	*
	*	====NOTICE====
	*	May need further work to make it more generic
	*
	*/
	private static function trimAvailablePeriods($from, $to, $p_allowed){
		foreach($p_allowed as $p){
			if($p["from"]>$to || $p["to"]<$from){
				$p=null;
			}else{
				$p["from"]=max($p["from"],$from);
				$p["to"]=min($p["to"],$to);
			}
		}
		return $p_allowed;
	}
	/*
	*
	*	Helper function used by makeAvailablePeriods
	*	divide periods of time by other periods of time
	*
	*/
	private static function makeDividedPeriods($toProcess,$divider){
		//exit(var_dump($divider));
		$result=array();
		foreach($toProcess as $p){
			foreach($divider as $d){
				$res=self::divideTime($p,$d);
				if($res[0])array_push($result,$res[0]);
				$p=$res[1];
				if(!$res[1]){
					break;
				}
			}
			if($p){
				array_push($result,$p);
			}
		}
		return $result;
	}
	/*
	*
	*	Helper function used by makeDividedPeriods 
	*
	*/
	private static function divideTime($c0,$c1){
		$r0=array();
		$r1=array();
		if($c1["from"]<=$c0["from"]){	//no periods before c1
			$r0=Null;
		}else{
			$r0["from"]=$c0["from"];
			$r0["to"]=min($c1["from"],$c0["to"]);
		}
		if($c1["to"]>=$c0["to"]){	//no periods after c1
			$r1=Null;
		}else{
			$r1["from"]=max($c1["to"],$c0["from"]);
			$r1["to"]=$c0["to"];
		}
		return array($r0,$r1);
	}
}