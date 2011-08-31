function markerFunction(){                                                      
	/* TODO: Wesley - getLocation should, given an oauthid, print out something that looks like what's in the markers div right now (below)  */  
	//alert("in marker:" + "/php-scripts/google_map_functions.php?access_token="+access_token+"&oauth_id=" + oauth_array[oauths_processed]);

		$.get("php-scripts/profile_functions.php?user_oauth_id="+user_oauth_id, function(data){  
		
		if(data != 0 && data != "0" && data != "error"){ 
			   
			var people = data.split(";");
		    for(var i = 0; i < people.length; i++){
				var arr = people[i].split("/");     
			 	if(arr.length > 0){
					var lat = arr[0];
					var lng = arr[1];  
					var name = arr[2];   
					var location = arr[3];    
					MYMAP.placeMarker(lat,lng, name, false, location); 
				} 
			}
			  
		}
		  
		
	});
	
	
}