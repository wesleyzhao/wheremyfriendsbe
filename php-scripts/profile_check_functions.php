<?php
require_once('php-scripts/mysql_connect.php');

function isInUsers($id){
	$id = intval($id);
	mysqlConnect();
	$res = mysql_query("SELECT name,access_token FROM users WHERE oauth_id='$id'");
	if (mysql_num_rows($res)){
	$row = mysql_fetch_array($res);
	if ($row['access_token'] == '') return false;
	else return true;
	}
	else return false;
}

function hasLoaded($id){
	$id = intval($id);
	mysqlConnect();
	$res = mysql_query("SELECT has_loaded FROM users WHERE oauth_id='$id'");
	if (mysql_num_rows($res)){
	//if user exists in database
		$row = mysql_fetch_array($res);
		$has_loaded = intval($row['has_loaded']);
		
		if ($has_loaded==1){
			return "<script type='text/javascript' src='/js/hasLoaded.js'></script>";
		}
		else{
		//if this is the first time the user is loading
			return "<script type='text/javascript' src='/js/hasNotLoaded.js'></script>";
		}
	}
	return false;
}


function hasLoadedBool($id){
	mysqlConnect();
	$id = intval($id);
	$res = mysql_query("SELECT has_loaded FROM users WHERE oauth_id='$id'");

	if (mysql_num_rows($res)){
	//if user exists in database
		$row = mysql_fetch_array($res);
		$has_loaded = intval($row['has_loaded']);
		
		if ($has_loaded==1){
			return true;
		}
		else{
		//if this is the first time the user is loading
		mysql_query("UPDATE users SET has_loaded='1' WHERE oauth_id='$id'");
			return false;
		}
	}
	return false;
}

function getFriendCount($id){
	mysqlConnect();
	$res = mysql_query("SELECT id FROM friends WHERE friend1_id='$id' OR friend2_id='$id'");
	$num = mysql_num_rows($res);
	if ($num) return $num;
	else return 0;
}


?>