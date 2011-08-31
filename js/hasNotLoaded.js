function markerFunction(){                                                      
	/* TODO: Wesley - getLocation should, given an oauthid, print out something that looks like what's in the markers div right now (below)  */  
	//alert("in marker:" + "/php-scripts/google_map_functions.php?access_token="+access_token+"&oauth_id=" + oauth_array[oauths_processed]);
		var oauth1 = oauth_array[oauths_processed];  
		var oauth2 = oauth_array[oauths_processed+1];
		var oauth3 = oauth_array[oauths_processed+2];
		var oauth4 = oauth_array[oauths_processed+3];
		var oauth5 = oauth_array[oauths_processed+4];
		var oauth6 = oauth_array[oauths_processed+5];
		var oauth7 = oauth_array[oauths_processed+6];
		var oauth8 = oauth_array[oauths_processed+7];
		var oauth9 = oauth_array[oauths_processed+8];
		var oauth10 = oauth_array[oauths_processed+9];
		var oauth11 = oauth_array[oauths_processed+10];
		var oauth12 = oauth_array[oauths_processed+11];
		var oauth13 = oauth_array[oauths_processed+12];
		var oauth14 = oauth_array[oauths_processed+13];
		var oauth15 = oauth_array[oauths_processed+14];
		var oauth16 = oauth_array[oauths_processed+15];
		var oauth17 = oauth_array[oauths_processed+16];
		var oauth18 = oauth_array[oauths_processed+17];
		var oauth19 = oauth_array[oauths_processed+18];
		var oauth20 = oauth_array[oauths_processed+19];
		var is_sql = 0;     
		if(oauths_processed < oauths_mysql_length){
			is_sql = 1;
		}
		$.get("php-scripts/google_map_functions.php?access_token="+access_token+"&user_oauth_id="+user_oauth_id+"&is_sql_friends="+0+"&oauth_id=" + oauth1 + ","  + oauth2 +","  + oauth3+","  + oauth4+","  + oauth5+ ","  +   oauth6+","  + oauth7+","  + oauth8+","  + oauth9+ ","  +   oauth10+ ","  +   oauth11+ ","  +   oauth12+ ","  +   oauth13+ ","  +   oauth14+ ","  +   oauth15+ ","  +   oauth16+ ","  +   oauth17+ ","  +   oauth18+ ","  +   oauth19+ ","  +   oauth20, function(data){  
		//$.get("php-scripts/google_map_functions.php?access_token="+access_token+"&user_oauth_id="+user_oauth_id+"&is_sql_friends="+is_sql+"&oauth_id=" + oauth1 + ","  + oauth2 +","  + oauth3+","  + oauth4+","  + oauth5+ ","  +   oauth6+","  + oauth7+","  + oauth8+","  + oauth9, function(data){  
		
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
			oauths_processed += 20; 
			if((oauths_processed+20) >= oauth_array.length){
			    var autopost = $("input[class='autopost']:checked").val();
				if(autopost == "on"){
					$.get("php-scripts/wall_post.php?access_token="+access_token+"&oauth_id="+user_oauth_id+"&friends_count="+friends_count+"&places_count="+places, function(data){
						return;   
					});      
				}   	
			}          
			else{
				markerFunction();
			}  
		}
		else{
		//keeps it from stopping at a bad resource
			oauths_processed +=20;
			if((oauths_processed+20) >= oauth_array.length){        
				var autopost = $("input[class='autopost']:checked").val();
				if(autopost == "on"){
					$.get("php-scripts/wall_post.php?access_token="+access_token+"&oauth_id="+user_oauth_id+"&friends_count="+friends_count+"&places_count="+places, function(data){
						return;   
					});      
				}             
			}
			else{
				markerFunction(); 
			}	
		}
		  
		
	});
	
	
}