<?php
    # Connect to memcache:
    //global $memcache;
    //$memcache = new Memcache;    
	//$memcache->connect('localhost', 11211) or die ("Could not connect");
    

	function mysql_query_cache($sql){ 
	//returns cached result as an array of rows
	//sets cache if result was not cached
	//returns array of mysql result rows
	   // global $memcache;
		//$key = md5('query'.$sql);
		//$result = $memcache->get($key);            
		//if($result == null) { 
			//print "$sql";  
			$qry = mysql_query($sql) or die(mysql_error()." : $sql");
			//if(mysql_num_rows($qry) > 0) {  			//this was done by Dan Shipper
			if(mysql_num_rows($qry)) { 
				while ($row  = mysql_fetch_array($qry)){
					$result[] = $row;
				}
				//$result = mysql_fetch_array($qry);		//this was  done by Dan Shiper
				
			}
			else{
				$result = array();
			}
			
			//$memcache->set($key,$result,0,3600);
		//}     
		      
		return $result;
	}
    
?>