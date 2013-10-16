<?php
require "BookingCheck.php";

class BookingController extends BaseController{
	public function __construct(){
		$this->beforeFilter('csrf',array('on'=>'post'));
		//$this->beforeFilter('earlyBar',array('only'=>array('postBook','getEdit','postEdit','getDelete')));
		$this->beforeFilter('permission:can_book',array('only'=>array('postBook','postEdit')));
		$this->beforeFilter('owner',array('only'=>array('getEdit','postEdit','getDelete')));
		$this->beforeFilter('permission:moderator',array('only'=>array('getExpire','getCheckin')));
	}
	public function getIndex(){
		return self::redirectHome();
	}
	public function getBook(){
		return View::make("booking/index");
	}
	public function postBook(){
		return self::postEdit(Null);
	}
	public function getEdit($id){
		$form=BookingForm::fillForm($id);
		return View::make("booking/index",array("booking"=>$form));
	}
	public function postEdit($id){
		$res=self::makeBooking($id);
		$succ=$res[0];
		$booking=$res[1];
		
		if($succ>0){
			return self::redirectHome("success","Your booking has been confirmed");
		}else if($succ==0){
			return View::make("booking/index",array("booking"=>$_POST, "errors"=>$booking->errors()));
		}else if($succ==-1){
			Session::flash("error","The desired time is not available");
			return View::make("booking/index",array("booking"=>$_POST));
		}
	}
	public function getExpire($id){
		$res=Booking::updateStatus($id,2);
		return self::redirectHome("success","Delete success");
	}

	public function getDelete($id){
		$res=Booking::updateStatus($id,0);
		return self::redirectHome("success","Delete success");
	}
	public function getCheckin($id){
		$res=Booking::updateStatus($id,3);
		return self::redirectHome("success","Checkin success");
		/*$from=$rec["from"];
		$to=$rec["to"];
		$now=new Datetime();
		if($now>$from && $now<$to){
			$rec["status"]=3;
			$rec->save();
			return "checkin success";
		}else if($now<=$from){
			return "Too early to check in";
		}else if($now>=$to){
			return "Booking expired";
		}*/
	}
	public function getAvailable($date){
		$date=new Datetime($date);
		$next_day=clone $date;
		$next_day=$next_day->add(new DateInterval("P1D"));
		return View::make("booking/available",array(
			"periods"=>BookingCheck::inquireAvailablePeriods($date,$next_day),
			"date"=>$date
			));
	}
	public function getTest(){
		$testcookie=Cookie::make("test","hello Jimmy!",5);
		return Response::make()->withCookie($testcookie);
		//return(var_dump(new Datetime("12:98")));
		//return(var_dump(Booking::getOnesBookings(12334)));
	}
	
	private static function makeBooking($id=Null){
		return BookingForm::saveForm($_POST,$id);
	}
	
	public static function redirectHome($msgType="",$msgContent=""){
		return Redirect::to("bookings/book")->with($msgType,$msgContent);
	}
	
	public static function test(){
		return "heeee";
	}
}

class BookingForm{


	public static function fillForm($id){
		$rec=Booking::find($id);
		$data=array();
		$data["title"]=$rec["title"];
		$data["date"]=$rec["from"]->format("Y-m-d");
		$data["from"]=$rec["from"]->format("H:i");
		$data["to"]=$rec["to"]->format("H:i");
		$data["note"]=$rec["note"];
		return $data;
	}
	
	public static function saveForm($data,$id=Null){
		if($id===Null){
			$booking=new Booking;
		}else{
			$booking=Booking::find($id);
		}
		
		$dts=self::calDates($data);
		if(!BookingCheck::isBookingAllowed($dts["from"],$dts["to"],$id))return array(0=>-1,1=>Null);
		
		$booking["title"]=$data["title"];
		$booking["user"]=Auth::user()->id;
		$booking["from"]=$dts["from"];
		$booking["to"]=$dts["to"];	
		$booking["note"]=$data["note"];

		return array(0=>$booking->save(),1=>$booking);
	}

	public static function calDates($package){
		$date_from=new DateTime($package["date"]);
		$date_to=clone($date_from);
		$time_from=explode(":",$package["from"]);
		$time_to=explode(":",$package["to"]);
		$date_from->setTime($time_from[0],$time_from[1]);
		$date_to->setTime($time_to[0],$time_to[1]);

		return array(
				"from"	=>	min($date_from,$date_to),
				"to"	=>	max($date_from,$date_to)
				);
	}
}











