<?php
require_once("mysql_connect.php");

$oauth_id = $_GET['oauth_id'];

mysql_query("UPDATE users SET has_loaded='0' WHERE oauth_id='$oauth_id'");

if ($oauth_id){
	header("location: http://wheremyfriends.be/profile.php?id=$oauth_id");
}
?>