<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get("",function(){
		return Redirect::to("bookings/book");
	});

Route::controller("bookings","BookingController");
Route::controller("manage","ManageController");

View::composer("booking/userbookings","BookingComposer");

//Route::resource("foo","fooController");


//Route::get('/', 'HomeController@showWelcome');

Route::get('oauthtest/{provider?}','OauthTest@action_session');



Route::get('phpversion',function(){
	return phpversion();
});



/*
function DownloadFile($file) { // $file = include path 
	//echo "wtfff";
	if(file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		//echo "success";
		exit;
		//return "success";
	}else{
		die("doesnot exist");
	}
}

Route::get('test',function(){
	DownloadFile("test.txt");
	#return stream_context_get_default();
});
*/


// Confide routes
Route::get( 'user/create',                 'UserController@create');
Route::post('user',                        'UserController@store');
Route::get( 'user/login',                  'UserController@login');
Route::post('user/login',                  'UserController@do_login');
Route::get( 'user/confirm/{code}',         'UserController@confirm');
Route::get( 'user/forgot_password',        'UserController@forgot_password');
Route::post('user/forgot_password',        'UserController@do_forgot_password');
Route::get( 'user/reset_password/{token}', 'UserController@reset_password');
Route::post('user/reset_password',         'UserController@do_reset_password');
Route::get( 'user/logout',                 'UserController@logout');
Route::post('user/update/{id}/{field}',         'UserController@update');