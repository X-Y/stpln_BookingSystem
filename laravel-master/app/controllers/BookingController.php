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
	
	public function getTest(){
		$today_begin=new Datetime((new Datetime())->format("Y-m-d 0:0:0"));
		$today_end=new Datetime((new Datetime())->format("Y-m-d 23:59:59"));
		BookingCheck::fetchPeriods($today_begin,$today_end);

	}
	public function getAvailable(){
		exit(var_dump(BookingCheck::makeAvailablePeriods(new Datetime("2014-12-26 0:0:0"),new Datetime("2014-12-26 23:59:59"))));
		/*$result=[];
		$toProcess=[
			[
				"from"=>(new DateTime("2013-8-31 0:0:0")),
				"to"=>(new DateTime("2013-8-31 12:00:00"))
			],
			[
				"from"=>(new DateTime("2013-8-31 13:0:0")),
				"to"=>(new DateTime("2013-8-31 23:59:59"))
			],

		];
		$unavailable=[
			[
				"from"=>(new DateTime("2013-8-30 5:0:0")),
				"to"=>(new DateTime("2013-8-30 7:0:0"))
			],
			[
				"from"=>(new DateTime("2013-8-31 5:0:0")),
				"to"=>(new DateTime("2013-8-31 7:0:0"))
			],
			[
				"from"=>(new DateTime("2013-8-31 6:0:0")),
				"to"=>(new DateTime("2013-8-31 9:0:0"))
			],
			[
				"from"=>(new DateTime("2013-9-1 20:0:0")),
				"to"=>(new DateTime("2013-9-1 21:0:0"))
			]
		];
		foreach($toProcess as $p){
			foreach($unavailable as $one){
				$res=self::divideTime($p,$one);
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
		exit(var_dump($result));*/
	}
	/*
	private static function divideTime($c0,$c1){
		$r0=[];
		$r1=[];
		if($c1["from"]<=$c0["from"]){
			$r0=Null;
		}else{
			$r0["from"]=$c0["from"];
			if($c1["from"]>=$c0["to"]){
				$r0["to"]=$c0["to"];
			}else{
				$r0["to"]=$c1["from"];
			}
		}
		if($c1["to"]>=$c0["to"]){
			$r1=Null;
		}else{
			if($c1["to"]<=$c0["from"]){
				$r1["from"]=$c0["from"];
			}else{
				$r1["from"]=$c1["to"];
			}
			$r1["to"]=$c0["to"];
		}
		return [$r0,$r1];
	}*/
}













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
	*	public function isBookingAllowed
	*	
	*
	*/
	public static function isBookingAllowed($from,$to){
		if(	self::isWithinBAD($from,$to) &&
			self::isRuleAllowed($from,$to) && 
			self::isTimeFree($from,$to)){
			return true;
		}else{
			return false;
		}
	}
	
	public static function makeAvailablePeriods($from,$to){
		$raw=self::fetchRawPeriods($from,$to);
		$p_Allowed=$raw["periods_allowed"];
		$p_Disallowed=$raw["periods_disallowed"];
		$p_toDivide=[
			[
			"from"=>$from,
			"to"=>$to
			]
		];
		$res_all_allowed=self::trimAvailablePeriods($from,$to,$p_Allowed);
		$res=self::makeDividedPeriods($res_all_allowed,$p_Disallowed);
		
		return $res;
	}
	/*
	*
	*	Get all the relavent periods for counting availablity 
	*
	*/
	public static function fetchRawPeriods($from,$to){
		$arr_allowed=[];
		$arr_disallowed=[];
		
		//Rule periods
		$rules=BookingRule::where("active","=",true)->get();
		foreach($rules as $rule){
			$fromInte=$rule["from"];
			$toInte=$rule["to"];
			$action=$rule['action'];
						
			//get the from and to rule time
			$func=self::$ruleSets[$rule["frequency"]];
			$initial=call_user_func_array(array("BookingCheck",$func),[$from,$to,$fromInte,$toInte]);
			$res=self::getRulePeriods($initial,$fromInte,$toInte);
			
			if($action){
				array_push($arr_allowed,$res);
			}else{
				array_push($arr_disallowed,$res);
			}
		}
		
		//Booking periods
		$bookings=Booking::where("from",">=",$from)->where("from","<=",$to)->get();
		//exit(var_dump($bookings));
		if(!empty($bookings)){
			foreach($bookings as $booking){
				array_push($arr_disallowed,["from"=>$booking["from"],"to"=>$booking["to"]]);
			}
		}
		
		function cmp($a,$b){
			return ($a["from"]<$b["from"])?-1:1;
		}
		usort($arr_allowed,"cmp");
		usort($arr_disallowed,"cmp");
		//exit(var_dump($arr_disallowed));
		return [
			"periods_allowed"=>$arr_allowed,
			"periods_disallowed"=>$arr_disallowed
			];
		//exit(var_dump([$arr_allowed,$arr_disallowed]));
	}
	
	
	
	
	
	/*
	*
	*	check if the booking is within 
	*	BOOKING_ALLOWED_DAYS, BAD. That is counting from
	*	the current moment.
	*
	*/
	private static function isWithinBAD($from,$to){
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
		$rules=BookingRule::where("active","=",true)->orderBy("frequency")->get();
		
		foreach($rules as $rule){
			$fromInte=$rule["from"];
			$toInte=$rule["to"];
			$action=$rule['action'];
						
			//get the from and to rule time
			$func=self::$ruleSets[$rule["frequency"]];
			$initial=call_user_func_array(array("BookingCheck",$func),[$from,$to,$fromInte,$toInte]);
			
			$res=self::getRulePeriods($initial,$fromInte,$toInte);
			$from_rule=$res["from"];
			$to_rule=$res["to"];
			
			//If the booking is following the rule with the corresponding action, it passes
			$pass=self::actionCheck($from,$to,$from_rule,$to_rule,$action);
			
			//If one rule is broken, it's not allowed
			if(!$pass)return false;
		}
		return true;
	}
	/*
	*
	*	isTimeFree checks if the booking period is occupied by other bookings.
	*
	*/
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
		
	/*
	*	Convert the rule stored in db to DateInterval objects.
	*	Month, day need to be reduced by 1 as they're added to 1
	*	instead of 0. 
	*
	*/
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
		
		return [
			"from"=>$from_rule,
			"to"=>$to_rule
			];
	}

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
		$result=[];
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
		$r0=[];
		$r1=[];
		if($c1["from"]<=$c0["from"]){
			$r0=Null;
		}else{
			$r0["from"]=$c0["from"];
			if($c1["from"]>=$c0["to"]){
				$r0["to"]=$c0["to"];
			}else{
				$r0["to"]=$c1["from"];
			}
		}
		if($c1["to"]>=$c0["to"]){
			$r1=Null;
		}else{
			if($c1["to"]<=$c0["from"]){
				$r1["from"]=$c0["from"];
			}else{
				$r1["from"]=$c1["to"];
			}
			$r1["to"]=$c0["to"];
		}
		return [$r0,$r1];
	}
}