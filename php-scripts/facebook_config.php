<?php
require('src/facebook.php');
require_once('memcache_functions.php');

$facebook = new Facebook(array(
	'appId' => '185236744851479',		//specific to PleaseMatchMe
	'secret' => '4245593f6906261e421ae29520774413',		//specific to PleaseMatchMe
	'cookie' => true,
	));


function isFbLogged(){
//reads the global $facebook variable
//returns the user fb_id if the user is signed on, else returns false
	global $facebook;
	$session = $facebook->getSession();
	if ($session){
		try{
			$uid = $facebook->getUser();
			$person = $facebook->api("/$uid");
			return $uid;
		}
		catch (FacebookApiException $e){
			return false;
		}
	}
	else{
		return false;
	}
}



function getTweet($ishome = false){
//add url with 
	$uid = isFBLogged();
	if ($uid && !$ishome) $profile = "profile.php?id=$uid";
	else $profile = '';
	$html = "<script src='http://platform.twitter.com/widgets.js' type='text/javascript'></script>
<a href='http://twitter.com/share' class='twitter-share-button' data-related='wesleyzhao,danshipper,ajaymehta' data-url='http://wheremyfriends.be/$profile' data-count='none' data-text='Im looking at a dynamic map of where my friends are located around the world! #wheremyfriendsbe'>Tweet</a>";
	return $html.'&nbsp;';
}

function getFbShare(){
	$uid = isFBLogged();
	if ($uid) $profile = "profile.php?id=$uid";
	else $profile = '';
	$url = urlencode("http://wheremyfriends.be/$profile");
	$text = urlencode("See a dynamic map of all your friends around the world! | Where My Friends Be");
	//$html = "<a name='fb_share' type='button' share_url='http://wheremyfrin' href='http://www.facebook.com/sharer.php'>Share</a><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'></script>";
	$html = "<script>function fbs_click() {u='http://wheremyfriends.be/$profile';t='$text';window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script><style> html .fb_share_link { font-size: 13px; padding:2px 0 0 20px; height:16px; background:url(http://static.ak.facebook.com/images/share/facebook_share_icon.gif?6:26981) no-repeat top left; }</style><a rel='nofollow' title='Map of all your friends in the world' href='http://www.facebook.com/share.php?u=<;url>' onclick='return fbs_click()' class='fb_share_link' target='_blank'>Share</a>";
	return $html.'&nbsp;';
}

function getLoginUrl(){
	global $facebook;
	$ref = $facebook->getLoginUrl(array(
		'req_perms'=>'user_location,friends_location,offline_access,publish_stream'
	));
	
	if ($ref){
		return $ref;
	}
	else return '';
}

function getUser($uid,$token=''){
global $facebook;
	$user = $facebook->api("/$uid",array('access_token'=>$token));
	return $user;
}

function getToken(){
	global $facebook;
	return $facebook->getAccessToken();
}

function getLoginButton($isIE = false){
	global $facebook;
	$uid = isFbLogged();  

	
	if (!$uid){
		if ($isIE){
			$ref = $facebook->getLoginUrl(array(
			'req_perms'=>'user_location,friends_location,offline_access,publish_stream'
			));
			return "<a href='$ref'>Login to find out</a>";
		}
	else{
		return "<fb:login-button perms='friends_location,user_location,offline_access,publish_stream'>Make your social map now!</fb:login-button>";
	}
	}
	else return "<a href='profile.php?id=$uid' alt='A map of all my friends around the world'>Go to your map</a>";
}  

function getSignUp($isIE=false, $hasLoaded = true){
	global $facebook;
	$uid = isFbLogged();  
	$tweet = getTweet();
	$fb = getFbShare();
	
	if (!$uid){
		if ($isIE){
			$ref = $facebook->getLoginUrl(array(
			'req_perms'=>'user_location,friends_location,offline_access,publish_stream'
			));
			return "<a href='$ref'>Click here to get your own page</a> $fb $tweet";
		}
		else{
			return "<fb:login-button perms='friends_location,user_location,offline_access,publish_stream'>Click here to get your own page</fb:login-button> or share: $fb $tweet";
		}
	}
	else{
		if($hasLoaded){
		    return "<div class='share-this'>share this with your friends </div> $fb $tweet";
		}
		else{
			return "<div class='share-this'>share this with your friends </div> $fb $tweet
		<div class='smaller-share'>post map to Facebook: <input class=autopost type='checkbox' name='autopost' value='autopost' checked='checked'/></div>";
		}
		  
	}
	
}

function getFriends($uid){
	//returns an array of friends ids
	global $facebook;
	$person = $facebook->api("/$uid/friends");
	$friends =$person['data'];
	$ids = array();
	foreach ($friends as $friend){
		$ids[] = $friend['id'];
	}
	return $ids;
}

function getFriendsCount($uid){
	mysqlConnect();
	global $facebook;
	$uid = intval($uid);
	$res = mysql_query_cache("SELECT access_token FROM users WHERE oauth_id='$uid'");
	try{
	$row = $res[0];
	$token = $row['access_token'];
	$person = $facebook->api("/$uid/friends",array('access_token'=>$token));
	$friends = $person['data'];
	return count($friends);
	}
	catch (Exception $e){
		return 999;
	}
}

function getJsFriends($uid){
	global $facebook;
	$ids = getFriends($uid);
	$oauth_array_fb = array();
	$oauth_array_mysql = array();
	mysqlConnect();
	
	/*
	foreach ($ids as $id){
		$uid = intval($id);
		$query = "SELECT id FROM users WHERE oauth_id = '$uid'";
		$res = mysql_query_cache($query);
		if (count($res)>0) $oauth_array_mysql[] = $id;
	}
	*/
	

	$query = "SELECT oauth_id FROM users WHERE oauth_id='";
	$query_part = implode("' OR oauth_id='",$ids);
	$query = $query.$query_part."'";
	//$res = mysql_query($query);
	$res = mysql_query_cache($query);
	
	if (count($res)>0){
		//while ($row = mysql_fetch_array($res)){
		foreach($res as $row){
			$oauth_array_mysql[] = $row['oauth_id'];
		}
		
	}
	else{
		
	}
	
	$oauth_array_fb = array_diff($ids,$oauth_array_mysql);
	$oauth_array_fb = array_values($oauth_array_fb);
	//return "WOBBLE";
	return "var oauth_array_fb = ".json_encode($oauth_array_fb).";\n var oauth_array_mysql = ".json_encode($oauth_array_mysql).";\n";
}

function getStumble(){
	return '<script src="http://www.stumbleupon.com/hostedbadge.php?s=4"></script>';
}

define('FACEBOOK_APP_ID', '185236744851479');
?>