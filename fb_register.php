<?php
	require_once("php-scripts/facebook_config.php");
	require_once("php-scripts/mysql_connect.php");
	$uid = isFbLogged();
	$uid = intval($uid);
	
	if ($uid){
		$token = getToken();
		mysqlConnect();
		
		$res = mysql_query("SELECT access_token FROM users WHERE oauth_id='$uid'");
		if (mysql_num_rows($res)){
			$row = mysql_fetch_array($res);
			$access_token =$row['access_token'];
			if ($access_token==""){
				mysql_query("UPDATE users SET access_token='$token' WHERE oauth_id='$uid'");
			}
			
		}
		else{
		//if user is not in database
			$user = getUser($uid,$token);
			$name = $user['name'];
			$uid  = mysql_escape_string($uid);
			$name = mysql_escape_string($name);
			$token = mysql_escape_string($token);
			mysql_query("INSERT INTO users (oauth_id,name,access_token) VALUES ('$uid','$name','$token')");
		}
		
	
		header("location: profile.php?id=$uid");
	}
	else{
		//print "this one";
		header("location: http://wheremyfriends.be/");
	}
?>