<?php
require_once("php-scripts/facebook_config.php");
require_once("php-scripts/head.php");


$using_ie8 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.') !== FALSE);
$using_ie7 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.') !== FALSE);
$using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE);
if ($using_ie8 || $using_ie7 || $using_ie6) $ie = true;
else $ie = false;

?>
<body>
<title>Maps of Your Friends Around the World | Where My Friends Be?</title>

<div id="heading">
	where my friends be?
</div>     

<div id="subheading">
	See and share an animated map of all your facebook friends!<br> <?php echo getFbShare(); echo getTweet(true);?>  <script src="http://www.stumbleupon.com/hostedbadge.php?s=4"></script>
	<a href="<?php echo getLoginUrl();?>"><img id="main-image" src="images/screen.png" alt="a world map showing all of your friends based on their current location"/></a>
   <div id="fb-bar"> <center><div id="fb-button"><?php echo getLoginButton($ie);?></div></center>  </div> 
</div> 

        
<div id="subsubheading">
	because your sprawling global network of friends makes you more worldy, right?
</div>

<div id="fb-root"></div>
     <script src="http://connect.facebook.net/en_US/all.js"></script>
     <script>
        FB.init({ 
           appId:'<?=FACEBOOK_APP_ID?>', cookie:true, 
           status:true, xfbml:true 
        });
     FB.Event.subscribe('auth.login', function(response) {
       window.location = 'fb_register.php';
     });
</script>
<script>function fb_login_click() {window.open('<?php echo getLoginUrl();?>','See a map of your friends around the world | WhereMyFriends.Be','toolbar=0,status=0,width=930,height=481');return false;}</script>
<?php   
require_once("php-scripts/footer.php");
?>