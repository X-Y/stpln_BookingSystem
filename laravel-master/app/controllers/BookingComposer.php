<?phpclass BookingComposer{	public function compose($view){		$scope=Input::get("scope");		if($scope=="active" || $scope===Null){			$scope=1;		}else if($scope=="all"){			$scope=3;		}		$bookings=array();		if(Auth::check()){			$bookings=Booking::getUserBookings(Auth::user()->id,$scope);		}		$view->with("userbookings",$bookings);	}}