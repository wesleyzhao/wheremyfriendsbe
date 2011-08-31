<?php
require_once('mysql_connect.php');
require_once('memcache_functions.php');

$user_oauth_id = $_GET['user_oauth_id'];
$user_oauth_id = mysql_escape_string($user_oauth_id);

echo getFriendLocations($user_oauth_id);

function getFriendLocations($oauth_id){
	//@param oauth_id: string/int id of the user
	//returns all friends of the user in the following form
	//[user_lat]/[user_lng]/[user_name],[user2_lat]/[user3_lng]/[user_name],etc
	mysqlConnect();
	$returnString = "";
	$ids_array = array();
	$queryString = "SELECT name,lat,lng,location FROM users WHERE";
	//$res = mysql_query("SELECT friend2_id FROM friends WHERE friend1_id='$oauth_id'");		//pre-memcache
	//$res2 = mysql_query("SELECT friend1_id FROM friends WHERE friend2_id='$oauth_id'");		//pre-memcache
	$oauth_id = intval($oauth_id);
	
	$res = mysql_query_cache("SELECT friend2_id FROM friends WHERE friend1_id='$oauth_id'");		//post-memcache
	$res2 = mysql_query_cache("SELECT friend1_id FROM friends WHERE friend2_id='$oauth_id'");		//post-memcache
	//if (mysql_num_rows($res)){		//pre memcache
	if (count($res)>0){	
		//while ($row = mysql_fetch_array($res)){			//pre memcache
		foreach ($res as $row){
			if ($row['friend2_id']!='')
			$queryString = $queryString." oauth_id='{$row['friend2_id']}' OR";
		}
	}
	//if (mysql_num_rows($res2)){			//pre memcache
	if (count($res2)>0){
		//while ($row = mysql_fetch_array($res2)){			//pre memcache
		foreach ($res2 as $row){
			if ($row['friend1_id']!='')
			$queryString = $queryString." oauth_id='{$row['friend1_id']}' OR";
		}
	}
	
	$query = substr($queryString,0,(strlen($queryString)-3));
	//$res3 = mysql_query($query);			//pre memcache
	$res3 = mysql_query_cache($query);
	
	//if (mysql_num_rows($res3)){		//pre memcache
	if (count($res3)>0){
		$commaString = "";
		//while ($row = mysql_fetch_array($res3)){			//pre memcache
		foreach ($res3 as $row){
			if ($returnString!="") $commaString = ";";
			$returnString = $returnString.$commaString."{$row['lat']}/{$row['lng']}/{$row['name']}/{$row['location']}";
		}
	}
	return $returnString;
}

function getData($oauth_id){
//takes one oauth_id
//returns the formatted string with lat/lng/name
//returns false if not found
	mysqlConnect();
	$res = mysql_query("SELECT lat,lng,name FROM users WHERE oauth_id='$oauth_id'");
	if (mysql_num_rows($res)){
		$row = mysql_fetch_array($res);
		$returnString = "{$row['lat']}/{$row['lng']}/{$row['name']}";
		return $returnString;
	}
	else{
		return false;
	}
}

?>
