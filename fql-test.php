<?php

require("php-scripts/facebook_config.php");
      
$uid = isFbLogged();
$token = $facebook ->getAccessToken();
  
$url = "https://api.facebook.com/method/fql.query?access_token=$token&query=";
$query = "SELECT name,current_location FROM user WHERE uid=$uid ";

$user = $facebook->api("/$uid?$token");
$person = $facebook->api("/$uid/friends");
$friends = $person['data'];

           
for ($i = 0;$i<100;$i++){
//foreach ($friends as $fr){
	$fr = $friends[$i];	
	$query = $query."OR uid={$fr['id']} ";
}             
  
$params = array(
	    'method' => 'fql.query',
	    'query' => $query,
	);

	//Run Query
	$result = $facebook->api($params);
    $i = 0; 
	foreach ($result as $person){
		print "$i: ".$person['current_location']['name']."<br>";
		$i+=1;
	}
//print_r($result);             

?>