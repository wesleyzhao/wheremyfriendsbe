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
<script type="text/javascript"
 src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js">
</script>
</head>
<?php	require_once("php-scripts/facebook_config.php"); 
$name = "";

?>
<body > 
<?php echo getLoginButton();?>
	<?php print hasLoaded($user_oauth_id); ?>  
	<script type="text/javascript"> 
        
		/*TODO: Wesley - make oauth_array into an array with all of the oauth_id's with a php function */
		<?php 
			$uid = isFbLogged();
			if ($uid){
				$friends = getFriends();
				$friends_count = count($friends); 
				$user = getUser($uid);
				$name = $user['name'] ;
				echo "var oauth_array = ".json_encode($friends).";\n";
				echo "var user_oauth_id= '".$uid."';\n";
				echo "var access_token = '".getToken()."';\n"; 
			 
				
			}
			else{
				echo "var oauth_array = ['12312434']; \n";
			}
			
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
						homeLat = parseFloat(lat);
						homeLon = parseFloat(lng); 
						myLatLng = new google.maps.LatLng(homeLat, homeLon);  
						isHomeMarked = MYMAP.placeMarker(lat,lng, name, true);    
						
					}	
				   
				} 
			});
					
			 
		}
		
		MYMAP.placeMarker = function(lati, lng, name, special) {
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
					if(markers[lat+lng].indexOf("Home user") != -1){
						special = true;
					}     
				}
				else{
					places +=1;
					if(special){
						markers[lat+lng] = "<b>Friends here:</b> Home user"; 
					} 
					else{
						markers[lat+lng] = "<b>Friends here:</b> " + name; 
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
			
				//MYMAP.map.fitBounds(MYMAP.bounds);  
				return true;
			}                                     
		}     

		$(document).ready(function() {    
		  $("#map").css({
		    height: 500,
		    width: 600
		  });
		  var myLatng = new google.maps.LatLng(17.74033553, 83.25067267);
		  MYMAP.init('#map_canvas', myLatng, 4);
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
        window.location.reload();
      });
 </script>
   <?php print $name; ?> has <?php print $friends_count; ?> friends in <span id="places">0</span> places.
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>