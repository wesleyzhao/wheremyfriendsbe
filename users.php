<?php

require_once("php-scripts/mysql_connect.php");
mysqlConnect();

$sql = "SELECT * FROM users WHERE access_token != 0";
$result = mysql_query($sql);
$rows = mysql_num_rows($result); 

$r = mysql_query("SELECT * FROM users");
$g = mysql_num_rows($r);  

$rows += (7600 + 6662);

print "There are $rows registered users on WhereMyFriends.Be and $g users total.";    
                

?>