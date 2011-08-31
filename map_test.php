<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 100% }
</style>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<?php
	/*
	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=Seattle,WA&sensor=false";
	$data = file_get_contents($url);
	$jsondata = json_decode($data,true);
	$location =  $jsondata['results'][0]['geometry']['location'];
	$lat = $location['lat'];
	$lng = $location['lng'];
	echo "Lat: $lat and Lng: $lng";
	*/
	
try{
require('src/facebook.php');
}
catch (Exception $e){
	echo 'error: '.$e;
}
echo 'here4';
$facebook = new Facebook(array( 
	'appId' => '196367720392808',
	'secret' => 'a4a60bf109bbc610f1b13c737e0ffffd',
	'cookie' => true,
	
	));
echo 'here3';
function isLogged(){
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


if (isLogged()) {
	$ref = $facebook->getLogoutUrl();
	$log = "log-out";
	echo 'is logged in here';
}	
else{
	$ref = $facebook->getLoginUrl(array(
		'req_perms'=>'user_location,friends_location'
	));
	$log = 'log-in';
	echo 'is logged out here';
}

echo "<a href='$ref'>$log</a>";


	function getLatLng($addressStr){
	//inputs a formatted text address string 
	//returns an array with 'lat' and 'lng' keys corresponding to lattitude and longitude
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$addressStr."&sensor=false";
		$data = file_get_contents($url);
		$jsondata = json_decode($data,true);
		$location =  $jsondata['results'][0]['geometry']['location'];
		return $location;
	}
	
	function makeLatLng($location,$varName = "latlng"){
	//inputs $location array with 'lat' and 'lng' variables
	//@param varName is the name it is
		$lat = $location['lat'];
		$lng = $location['lng'];
		return "var $varName = new google.maps.LatLng($lat,$lng);\n";
	}
	
	function placeMarker($LatlngObj, $markerName, $index="1"){
		$script = "var $markerName = new google.maps.Marker({ position: $LatlngObj,zIndex: $index}); \n markers.push($markerName);\n";
		return $script;
	}
	
	function placePath($LatlngObjHome, $LatlngObjPlace, $pathName){
		$script = "var flightPlanCoordinates = [$LatlngObjHome,$LatlngObjPlace];\n
	var $pathName = new google.maps.Polyline({
    path: flightPlanCoordinates,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });\n markers.push($pathName);\n";
		return $script;
	}
	
	function makeStringLocation($string){
	//inputs facebook string, converts to google url api string
	$arr1 = explode(', ',$string);
	$string = makeGoogleString($arr1);
	return $string;
	}
	
	function addPlus($arr){
		//must have 2 indexs 0,1
		$arr[0] = str_replace(' ','+',$arr[0]);
		$arr[1] = str_replace(' ','+',$arr[1]);
		return $arr;
	}

	function makeLocation($arr){
		$string = implode(",",$arr);
		return $string;
	}

	function makeGoogleString($arr){
		$arr = addPlus($arr);
		return makeLocation($arr);
	}
	function completeMarker($fb_location,$latLngObj,$markerObj,$latLngObjHome){
	//inputs a facebook string location
	//inputs the string name of the Latlng obj
	//inputs the string name of the Marker obj
	//returns scripts to place marker
		$location = makeStringLocation($fb_location);
		$arr = getLatLng($location);
		$makeLatlng = makeLatLng($arr,$latLngObj);
		$placer = placeMarker($latLngObj,$markerObj);
		$pather = placePath($latLngObjHome,$latLngObj,"path".$markerObj."");
		$script = $makeLatlng.$placer.$pather;
		return $script;
	}
	
?>
<script type="text/javascript">
	var markers = [];
<?php


if (isLogged()){
	$uid = isLogged();
	$token = $facebook ->getAccessToken();
	
	$user = $facebook->api("/$uid?$token");
	$loc = $user['location']['name'];
	$homeLoc = makeStringLocation($loc);
	$arr = getLatLng($homeLoc);
		$lat = $arr['lat'];		//set home lat
		$lng = $arr['lng'];		//set home lng
	echo makeLatLng($arr,"homeLatlng");
	$person = $facebook->api("/$uid/friends");
	$friends =$person['data'];
	
	$count =strval(count($friends));
	$count = count($friends);
	
	$num = 0;
	
	for ($i = 0;$i<30;$i++){
	//foreach ($friends as $fr){
		$fr = $friends[$i];	
		$friend = $facebook->api("/{$fr['id']}?access_token=$token");
		try{
			$frLoc = $friend['location']['name'];
			if ($frLoc){
				echo completeMarker($frLoc,"Latlng".strval($i),"marker".strval($i),"homeLatlng");
			}
			else{
				//echo '<br><i>location info is really private</i>';
			}
		}
		catch (Exception $e){
			//echo "caught.";
		}
		
	}
}
else{
	echo "ELSEEEEEEE";
}


	$arr = getLatLng($homeLoc);
	//$arr2 = getLatLng("Stanford,CA");
	//echo makeLatLng($arr2,"myLatlng2");
	echo makeLatLng($arr,"myLatlng");
	//echo placeMarker("myLatlng2","marker2");
	
	//echo completeMarker("San Francisco, CA","Latlng3","marker3");
?>

	 
 var marker = new google.maps.Marker({
      position: myLatlng,
      title:"Hello World!"
  });
 
  markers.push(marker);
 
  
  function initialize() {
    var latlng = new google.maps.LatLng(<?=$lat?>, <?=$lng?>);
    var myOptions = {
      zoom: 5,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
	for (mark in markers){
		markers[mark].setMap(map);
	}
	marker.setMap(map); 
  }
  
  

</script>
</head>
<body onload="initialize()">

  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>