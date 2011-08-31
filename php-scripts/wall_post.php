<?php
require_once('facebook_config.php');

$access_token = $_GET['access_token'];
$oauth_id = $_GET['oauth_id'];
$friend_count = $_GET['friends_count'];
$places_count = $_GET['places_count'];

$attach = array(
		'access_token'=>"$access_token",
		//'message'=>"WTF? I'm matched with some weird people....",
		'name'=>"I have $friend_count friends in $places_count different places around the world! How many do you have?",
		'link' =>"http://wheremyfriends.be/profile.php?id=$oauth_id",
		'description'=>"See a dynamic map of all your friends around the world | Where My Friends Be?",
		'picture'=>'http://wheremyfriends.be/images/facebook-post-image.png');
  	
	   
	$facebook->api("/$oauth_id/feed",'POST',$attach);

?>
