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

Route::controller("book","BookingController");

Route::resource("foo","fooController");

Route::get('/', 'HomeController@showWelcome');

Route::get('oauthtest/{provider?}','OauthTest@action_session');

Route::get('users', function()
{
	$users=User::all();
	//return $users;
	return View::make('hw')->with("users",$users);
});

Route::get('updateUsers',function()
{
	$users=User;
	return "retouch success!";
});

Route::get('phpversion',function(){
	return phpversion();
});


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