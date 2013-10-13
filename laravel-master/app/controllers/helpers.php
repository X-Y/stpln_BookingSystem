<?php

class Helper{
	
	public static function queryPermission($uid,$perm){
		/*
		//This part should be the correct implementation. 
		//It should be prefered when the auth server is 
		//established.
		
		$token="bla";	//token of this app
		return self::authCall($uid,$token,$perm);
		*/
		
		//Right now we use something simpler
		$role=User::find($uid)->role;
		$pv=0;
		switch($perm){
			case "can_book":
				$pv=1 << 0;
				break;
			case "moderator":
				$pv=1 << 1;
				break;
		}
		if($role & $pv)
			return true;
		else
			return false;
		
	}
	public static function authCall($uid,$token,$perm){
		//It's only used if auth server is there		
		$ch=curl_init("http://localhost/public/bookings/test");
		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		
		$res=curl_exec($ch);
		//exit(var_dump($res));
		$curl_close($ch);
		return $res;
	}
	public static function noPermissionRedirect(){
		return Redirect::back()->with("error","You don't have permission to do so");
	}
	public static function needsLoginRedirect(){
		return Redirect::back()->with("error","You must log in first");
	}

}