<?php
require_once('php-scripts/facebook_config.php');
require_once('php-scripts/mysql_connect.php');
require_once('php-scripts/profile_check_functions.php');  
require_once('php-scripts/memcache_functions.php');

$user_oauth_id = mysql_escape_string($_GET['id']);
$redirect = false;
if ($user_oauth_id == ''){
	header("location: http://wheremyfriends.be/");
}
else{
	mysqlConnect();
	$user_oauth_id = intval($user_oauth_id);
	
	$res = mysql_query_cache("SELECT name,access_token FROM users WHERE oauth_id='$user_oauth_id'"); 
	if (count($res)>0){
		$row = $res[0];
		if ($row['access_token'] == ''){
		 	$redirect = true;          
			print "in if";
			header("location: http://wheremyfriends.be/");  
		}
	} 
	else{
	   $redirect = true;    
	   header("location: http://wheremyfriends.be/");
	}
}            
	
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta property="og:image" content="http://wheremyfriends.be/images/facebook-post-image.png" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
</style>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?sensor=false">
</script>    
<script type="text/javascript"
 src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
</script>                                                              
<link rel="stylesheet" href="style/style.css" type="text/css"/> 
<LINK REL="SHORTCUT ICON" HREF="favicon.ico">  
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1108031-16']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<?php	
$name = "";

?>
<body > 
<div id="heading">
	<a style="color: white; text-decoration: none;" href="/">where my friends be?</a>
</div>     
	<?php 

	echo hasLoaded($user_oauth_id);      
		if(!$redirect){
			$has_loaded = hasLoadedBool($user_oauth_id);
		}
			$user = getUser($user_oauth_id);
			$name = $user['name'];

	?> 
	<title>Map of <?=$name?>'s Friends All Over the World| Where My Friends Be</title>
	<script type="text/javascript"> 
        
		/*TODO: Wesley - make oauth_array into an array with all of the oauth_id's with a php function */
		<?php 
				
				$uid = isFbLogged();
				if ($uid && $uid == $user_oauth_id){
					$friends = getFriends($user_oauth_id);
					$friends_count = count($friends); 
					echo getJsFriends($user_oauth_id);   
				}
				else{
					$friends_count = getFriendsCount($user_oauth_id);
					//$friends_count = 999;
					echo "var oauth_array_mysql = new Array();\n var oauth_array_fb = new Array();\n";
					
				}
				
				
				
				echo "var home_user = '".addSlashes($name)."';\n";
				echo "var friends_count = '".$friends_count."';\n";
				echo "var user_oauth_id= '".$user_oauth_id."';\n";
				echo "var access_token = '".getToken()."';\n"; 
			
		?>
	    var places = 0;
		var interval = null;  
		var homeLat = 0.0;
		var homeLon = 0.0;
		var isHomeMarked = false;
		var myLatLng = null;
		var markers = new Array();
		var MYMAP = {
		  bounds: null,
		  map: null
		}      

		MYMAP.init = function(selector, latLng, zoom) {
		  var myOptions = {
		    zoom:zoom,
		    center: latLng,
		    mapTypeId: google.maps.MapTypeId.ROADMAP
		  }
		  this.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		  this.bounds = new google.maps.LatLngBounds();
		}
	    var oauths_processed = 0; 
		var oauths_mysql_length = oauth_array_mysql.length;
		var oauth_array = oauth_array_mysql.concat(oauth_array_fb); 
   		var coord_regex = /-?\d+\.\d+/
	    
		function placeHomeMarker(){
			$.get("php-scripts/google_map_functions.php?access_token="+access_token+"&user_oauth_id="+user_oauth_id+"&oauth_id="+user_oauth_id, function(data){  
				//alert("got data: " + data);
				if(data != 0 && data != "0" && data != "error"){  
					//alert("got: " + data); 
				    var arr = data.split("/");   
				 	if(arr.length > 0){ 
						var lat = arr[0];
						var lng = arr[1];    
						var name = arr[2];   
						var location = arr[3];
						homeLat = parseFloat(lat);
						homeLon = parseFloat(lng); 
						myLatLng = new google.maps.LatLng(homeLat, homeLon);  
						isHomeMarked = MYMAP.placeMarker(lat,lng, name, true, location);    
						
					}	
				   
				} 
			});
					
			 
		}
		
		MYMAP.placeMarker = function(lati, lng, name, special, location) {
		    // create a new LatLng point for the marker
		    var lat = lati;
		    var lng = lng;  
			if(lng == null || lat == null || !lat.match(coord_regex) || !lng.match(coord_regex)){  
				return false;
			}  
			else{
		    	var point = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));
	            if(isHomeMarked){          
					var flightPlanCoordinates = [myLatLng,point];
					var path = new google.maps.Polyline({
				    	path: flightPlanCoordinates,
				    	strokeColor: '#FF0000',
				    	strokeOpacity: 1.0,
				    	strokeWeight: 2,
						map: MYMAP.map,
						title: name 
			
				  	}); 
				}             
			    // extend the bounds to include the new point
			    //MYMAP.bounds.extend(point);  
                
				//now we figure out if we've already added this marker
				if(markers[lat+lng] != undefined ){
					markers[lat+lng] = markers[lat+lng] + ", " + name; 
					if(markers[lat+lng].indexOf(home_user) != -1){
						special = true;
					}                    
				}
				else{
					places +=1;
					if(special){
						markers[lat+lng] = "Friends in " + location + ": " + home_user; 
					} 
					else{
						markers[lat+lng] = "Friends in " + location + ": " + name; 
					}          
					
					
				}

			    // add the marker itself     
		   		var marker = null;
				if(special){
			    		marker = new google.maps.Marker({
				        	position: point,
					        map: MYMAP.map,                       
							title: markers[lat+lng],
							icon: "http://graph.facebook.com/"+user_oauth_id+"/picture?type=square",  
							zIndex: 10000000
		                   
				    	});  
						MYMAP.map.setCenter(new google.maps.LatLng(lat, lng));
						    
				}
				else{
						marker = new google.maps.Marker({
				        	position: point,
					        map: MYMAP.map,
							animation: google.maps.Animation.DROP,
							title: markers[lat+lng]
		                   
				    	});
					
				} 
				var infoWindow = new google.maps.InfoWindow();
		
				google.maps.event.addListener(marker, 'click', function() {
				        infoWindow.setContent(markers[lat+lng]);
				        infoWindow.open(MYMAP.map, marker);
				});
			    
				$("#places").html(places);             
			                                           
				return true;
			}
			return true;                                     
		}     

		$(document).ready(function() {    
		  $("#map").css({
		    height: 500,
		    width: 600
		  });
		  var myLatng = new google.maps.LatLng(39.1140530, -94.6274636);
		  MYMAP.init('#map_canvas', myLatng, 3);
          placeHomeMarker();

		 markerFunction();
		  
		});
		
	   
	</script>

	
 <div id="fb-root"></div>
      <script src="http://connect.facebook.net/en_US/all.js"></script>
      <script>
         FB.init({ 
            appId:'<?=FACEBOOK_APP_ID?>', cookie:true, 
            status:true, xfbml:true 
         });
      FB.Event.subscribe('auth.login', function(response) {
        window.location='fb_register.php';
      });
 </script>
   <div id="subheading"><?php print $name; ?> has <?php print $friends_count; ?> friends in <span id="places">0</span> places around the world.<br> <?php echo getSignUp(false,$has_loaded);?>   </div>
  <div id="map_canvas"></div> 
  <?php $uid = isFbLogged(); if ($uid == $user_oauth_id){?>
  <center><div class="reload">Map not working? Click <a href="/reload.php?oauth_id=<?php print $user_oauth_id; ?>">here</a> to reload it. Be patient this could take a minute or two!</div></center>
  <?php } else if (!$uid){?>
	<div class="make-your-own"><b>Think this map is totally sweet?</b><br> If so, like, <fb:login-button perms='friends_location,user_location,offline_access,publish_stream'>Get your own now!</fb:login-button></div>
  <?php }?>
  <br><div id="plug">If you like what you see, you should click to <a href="http://eepurl.com/cLq7Q">sign up for future updates</a> from our team. We won't disappoint. </div>
 
  
  
  <?php
  require_once("php-scripts/footer.php");   

  ?>
</body>
</html>