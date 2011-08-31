<?php
require_once("facebook_config.php");
require_once("mysql_connect.php");
require_once("memcache_functions.php");

$oauth_id = $_GET['oauth_id'];
	$oauth_arr = explode(",",$oauth_id);
	
$access_token = $_GET['access_token'];
$access_token = mysql_escape_string($access_token);
$user_oauth_id = $_GET['user_oauth_id'];
$user_oauth_id = mysql_escape_string($user_oauth_id);

$is_sql_friends = intval($_GET['is_sql_friends']);

echo makeAjax($oauth_arr,$access_token,$is_sql_friends);  
//check users that are facebook or not right away

	function getLatLng($addressStr){
	//inputs a formatted text address string 
	//returns an array with 'lat' and 'lng' keys corresponding to lattitude and longitude
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$addressStr."&sensor=false";  
		return checkLatLng($addressStr);
	}
	
	function checkLatLng($formattedAddress){
		mysqlConnect();
		$formattedAddress = mysql_escape_string($formattedAddress);
		//$res = mysql_query("SELECT `lat`,`lng` FROM `geocodes` WHERE `location`='$formattedAddress'");
		$res = mysql_query_cache("SELECT `lat`,`lng` FROM `geocodes` WHERE `location`='$formattedAddress'");
		//if (mysql_num_rows($res)){
		if (count($res)>0){
		//if the location currently exists
			$row = $res[0];
			$location = array();
			$location['lat'] = $row['lat'];
			$location['lng'] = $row['lng'];
			
			return $location;
		}
		else{
			$url = "http://trailertrack.me/redirect.php?address=".$formattedAddress;
			$data = file_get_contents($url);
			$jsondata = json_decode($data,true); 
			if ($jsondata['status']=="OVERY_QUERY_LIMIT") {				
			$url = "http://dilemmaroulette.com/redirect.php?address=".$formattedAddress;
			$data = file_get_contents($url);
			$jsondata = json_decode($data,true); 
				if ($jsondata['status']=="OVERY_QUERY_LIMIT") {
					mail("dshipper@gmail.com","Retard","Fix it");
					mail("ajayumehta@gmail.com","Retard","Fix this");
					mail("wesley.zhao@gmail.com","Retard","Fix it");
				}
			}
			$location =  $jsondata['results'][0]['geometry']['location'];
			mysql_query("INSERT INTO geocodes (location,lat,lng) VALUES('$formattedAddress','{$location['lat']}','{$location['lng']}')");
			return $location;
		}
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
	
	function makeAjax($oauth_arr,$token,$is_sql_friends=false){
	//gets an array of oauth_ids and returns the lat/lng/name followed by a comma
		global $facebook,$user_oauth_id;
		$returnString = "";
		
		if ($is_sql_friends) {
			$friends_arr = getFriendLocationsSql($oauth_arr);
			foreach ($friends_arr as $friend_id => $friend_arr){
				$lat = $friend_arr['lat'];
				$lng = $friend_arr['lng'];
				if (($lat != "") && ($lng != "")){  
						if ($returnString == "") $comma = "";
						else $comma = ';';
						$returnString =$returnString.$comma.$arr['lat']."/".$arr['lng']."/{$friend_arr['name']}/{$friend_arr['location']}";
						$returnString =$returnString.$comma.$lat."/".$lng."/{$friend_arr['name']}/{$friend_arr['location']}";
						createFriendship($user_oauth_id,$friend_id);
				}
			}
		}
		else{
			$friends_arr = getFriendLocations($oauth_arr,$token);
			
		foreach ($friends_arr as $friend_id=>$friend_arr){
			try{
				if ($friend_arr['location']){
					$location = makeStringLocation($friend_arr['location']);

					$arr = getLatLng($location);
		
					if ($arr['lat'] && $arr['lng']){
					//if the lat and lng are properly received
						if ($returnString == "") $comma = "";
						else $comma = ';';
						$returnString =$returnString.$comma.$arr['lat']."/".$arr['lng']."/{$friend_arr['name']}/{$friend_arr['location']}";
						addUser($friend_arr['name'],$friend_id,$friend_arr['location'],$arr['lat'],$arr['lng']);
						createFriendship($user_oauth_id,$friend_id);
					}
				}
				else{
					//if the friend location doesnt exist, continue loop
				}
			}
			catch (Exception $e){
				return "error";
			}
		}
		}
		return $returnString;
	}
	
	function getFriendLocations($friends_ids,$token=""){
	//takes in an oauth_array of ids, returns an array with 'name' key and 'location' value
		global $facebook;
		$query = "SELECT uid,name,current_location FROM user WHERE uid='";
		$friends_arr = array();
		mysqlConnect();
	
		$has_query_changed = true;
		
		/*
		foreach ($friends_ids as $id){

				$query = $query." uid='$id' OR";
				$has_query_changed = true;
		}
		*/
		
		if (count($friends_ids)==1){
			$query = $query."{$friends_ids[0]}'";
		}
		else if (count($friends_ids)>1){
			$query_part = implode("' OR uid='",$friends_ids);
			$query = $query.$query_part."'";
		}
		//print substr($query,0,(strlen($query)-3));
		if ($has_query_changed)
		{
			//$query = substr($query,0,(strlen($query)-3));
			$params = array(
			'method' => 'fql.query',
			'query' => $query,
			'access_token' =>$token
			);

		//Run Query
			$result = $facebook->api($params);

			foreach ($result as $person){
				$friends_arr[$person['uid']]=array('location'=>$person['current_location']['name'],'name'=>$person['name']);
			}
		}
		
		return $friends_arr;
	}
	
	function addUser($name,$oauth_id,$location,$lat,$lng,$token=""){
	//adds users to database
	//if user already existed, return false
	//if user was added successfully, return true
	global $user_oauth_id;
		mysqlConnect();
		$name = mysql_escape_string($name);
		$oauth_id = mysql_escape_string($oauth_id);
			$oauth_id = intval($oauth_id);
		//$res = mysql_query("SELECT name FROM users WHERE oauth_id = '$oauth_id'");
		$res = mysql_query_cache("SELECT name FROM users WHERE oauth_id = '$oauth_id'");
		if (count($res)==0){
			$lat = mysql_escape_string($lat);
			$lng = mysql_escape_string ($lng);
			$token = mysql_escape_string($token);
			$location = mysql_escape_string($location);
			mysql_query("INSERT INTO users (name,oauth_id,lat,lng,access_token,location) VALUES('$name','$oauth_id','$lat','$lng','$token','$location')");
			return true;
		}
		else if ($user_oauth_id == $oauth_id){
			$lat = mysql_escape_string($lat);
			$lng = mysql_escape_string ($lng);
			$location = mysql_escape_string($location);
			mysql_query("UPDATE users SET lat = '$lat', lng='$lng',location='$location' WHERE oauth_id='$oauth_id'");
			return true;
		}
		return false;
		
	}
	
	function createFriendship($friend1_id,$friend2_id){
	//returns true if friendship is successfully added
	//returns false if friendship was already in database 
		if ($friend1_id != $friend2_id){
			mysqlConnect();
			$id1 = mysql_escape_string($friend1_id);
			$id2 = mysql_escape_string($friend2_id);
			$id1 = intval($id1);
			$id2 = intval($id2);
			//$res = mysql_query("SELECT id FROM friends WHERE (friend1_id = '$id1' AND friend2_id='$id2') OR (friend2_id='$id1' AND friend1_id='$id2')");
			$res = mysql_query_cache("SELECT id FROM friends WHERE (friend1_id = '$id1' AND friend2_id='$id2') OR (friend2_id='$id1' AND friend1_id='$id2')");
			//checks to see if friendship is already in as friend1-friend2 or friend2-friend1
			if (count($res)==0){
				mysql_query("INSERT INTO friends (friend1_id,friend2_id) VALUES('$id1','$id2')");
				return true;
			}
			else{
				return false;
			}
		}
		else return false;
	}
	
	function getFriendLocationsSql($friends_ids){
		//takes an array of oauth_ids and returns an array with id as key and an array ('name','location','lat','lng') as values
		$arr = array();
		
		if (count($friends_ids) == 0) {
			$query = "";
		}
		else if (count($friends_ids) == 1){
			$friend_id = intval($friends_ids[0]);
			$query = "SELECT oauth_id,name,location,lat,lng FROM users WHERE oauth_id='{$friends_ids[0]}'";
		}
		else{
			$query = "SELECT oauth_id,name,location,lat,lng FROM users WHERE oauth_id='";
			$query_part = implode("' OR oauth_id='",$friends_ids);
			$query = $query.$query_part."'";
		}
		mysqlConnect(); 
	   
		$res = mysql_query($query);
		if (mysql_num_rows($res)){
			while ($row = mysql_fetch_array($res)){
				$arr[$row['oauth_id']] = array('name'=>$row['name'],'location'=>$row['location'],'lat'=>$row['lat'],'lng'=>$row['lng']); 
				 
			}
		}       
		
		/*
		mysqlConnect();
		foreach ($friends_ids as $id){
			$id = intval($id);
			$res = mysql_query("SELECT name,location,lat,lng FROM users WHERE oauth_id='$id'");
			$row = mysql_fetch_array($res);
			$arr[$id] = array('name'=>$row['name'],'location'=>$row['location'],'lat'=>$row['lat'],'lng'=>$row['lng']); 
		}
		*/
		return $arr;
	}
?>