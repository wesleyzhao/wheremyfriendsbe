<?php
require_once("php-scripts/mysql_connect.php");
require_once("php-scripts/facebook_config.php");

mysqlConnect();
$res = mysql_query("SELECT name,oauth_id,access_token FROM users WHERE access_token!=''");

$arr = array();
 $person_count = mysql_num_rows($res);$fail_count = 0;
while ($row = mysql_fetch_array($res)){
	try{
		$person  = getUser($row['oauth_id'],$row['access_token']);
		$email = $person['email'];
		if ($email !=''){
			$arr[] = $email;
			mysql_query("INSERT INTO emails (name,oauth_id,email) VALUES ('{$row['name']}','{$row['oauth_id']}','$email')");
		
		}
	}
	catch (Exception $e){
		$fail_count = $fail_count+1;
	}

	
}

$email_count = count($arr);
 
$str = explode(",",$arr);
echo "Total people: ".strval($person_count)." Total emails: ".$strval($email_count)." Total fails ".$fail_count."<br>";
echo $str;
?>