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
});


App::after(function($request, $response)
{
	//
});

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
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
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


require("controllers/BookingController.php");
Route::filter('earlyBar', function($route,$request){
	$id=$route->getParameter("v1");
	if($id){
		$dt=Booking::find($id);
	}else{
		$dt=BookingForm::calDates(Input::all());
	}
	$BNET=Config::get("app.BOOKING_NO_EARLIER_THAN");
	$book_starting=(new Datetime())->add(new DateInterval("PT".$BNET."H"));
	if($book_starting>$dt["from"] && $dt["from"]>(new Datetime()))
		return Redirect::back()->with("error","You can't make/edit/delete bookings happening within ".$BNET." hours.");	
});

function queryPermission($uid,$perm){
	$token="bla";
	return authCall($uid,$token,$perm);
}
function authCall($uid,$token,$perm){
	$curl=curl_init("http://localhost/public/test");
	$res=curl_exec($curl);
	exit(var_dump($res));
	$curl_close($curl);
}
function noPermissionRedirect(){
	return Redirect::back()->with("error","You don't have permission to do so");
}

Route::filter('permission',function($route,$request,$value){
	$uid=12334;
	$res=queryPermission($uid,$value);
	if(!$res){
		return noPermissionRedirect();
	}
});

Route::filter('owner',function($route,$request){
	$id=Input::get("id");
	if($id==null)$id=$route->getParameter("v1");
	$uid=12335;
	$moderator=queryPermission($uid,"moderator");
	$booking=Booking::find($id);
	//exit(var_dump($booking->user!=$uid));
	if(!$moderator && $booking->user!=$uid){
		return noPermissionRedirect();
	}
});
