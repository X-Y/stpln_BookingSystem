<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
	//exit(var_dump(cookieAuth()));
	cookieAuth();
});


App::after(function($request, $response)
{
	//
});
function cookieAuth(){
	$uid=Cookie::get("fabrica_sso_authorised");
	if($uid)
		Auth::loginUsingId($uid);
	else
		Auth::logout();
}
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('user/login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

Route::filter("cookieAuth", function(){
	exit("Tonny!");

});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});


Route::filter('earlyBar', function($route,$request){
	$id=$route->getParameter("v1");
	if($id){
		$dt=Booking::find($id);
	}else{
		$dt=BookingForm::calDates(Input::all());
	}
	$BNET=Config::get("app.BOOKING_NO_EARLIER_THAN");
	$now=new Datetime();
	$book_starting=$now->add(new DateInterval("PT".$BNET."H"));
	if($book_starting>$dt["from"])
		return Redirect::back()->with("error","You can't make/edit/delete bookings happening within ".$BNET." hours.");	
});

Route::filter('permission',function($route,$request,$value){
	if(!Auth::check()){
		return Helper::needsLoginRedirect();
	}
	
	$uid=Auth::user()->id;
	$res=Helper::queryPermission($uid,$value);
	if(!$res){
		return Helper::noPermissionRedirect();
	}

});

Route::filter('owner',function($route,$request){
	if(!Auth::check()){
		return Helper::needsLoginRedirect();	
	}
	$id=Input::get("id");
	if($id==null)$id=$route->getParameter("v1");	
	
	$uid=Auth::user()->id;	//uid of the user. Should be implemented differently.
	$moderator=Helper::queryPermission($uid,"moderator");
	$booking=Booking::find($id);
	if(!$moderator && $booking->user!=$uid){
		return Helper::noPermissionRedirect();
	}
	
	
});
