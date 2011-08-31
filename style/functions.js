	//prefixLookup();
 	//cardValue();
	alert('functions loaded');
	
	var value_complete = false;
	var brand_complete = false;
	
	function cardBrand(){
		var prefix = $('input[name$="keyword"]').val();
		$('input[name$="keyword"]').keyup(function{
		// TODO replace with real val file
			$.getJSON("scripts/brandlookup?keyword=" + prefix, function(data){
				var brandList = $.parseJSON(data);
				$("#results").append(brandList.item1 + brandList.item2 + brandList.item3 + brandList.item4);
			});
		}
	}
	
	function cardValue(){
		//$('input[name$="balance"]').focusout(function{
			var cardBalance = $('input[name$="balance"]').val();
			if(cardBalance > 0.0){
				// TODO replace with real val file
				$.get("scripts/val?balance=" + cardBalance, function(data){
					if(data == -1){
						//Error - change it back to a minimum of 20
						$('input[name$="balance"]').val('20.00');
						$("#balance-message").html("<strong><font color="red">AvantCard only supports cards over $20 in value at this time. Please use a card with more than $20 balance.</font></strong>");
						$("#balance-checkbox").html("");
						$("contract-price").html("");
						value_complete = false;
					} 
					else if (data == 0){
						// no change to be made, cardBalance is a multiple of 5
						$("#balance-message").html("");
						$("#balance-checkbox").html("<img src='images/check.png' class='check'>");
						// TODO replace with real check image
						value_complete = true;
						cardPrice();
					}
					else {
						// need to change cardBalance to this number
						$('input[name$="balance"]').val(data);
						$("#balance-message").html('<strong><font color="red">AvantCard only supports cards in multiples of $5. Please use a card with a balance in multiples of $5.</font></strong>');
						$("#balance-checkbox").html("");
						$("contract-price").html("");
						value_complete = false;
					}
				});
			}
		//});		
	}
	
	function cardPrice(){
		var bal = $('input[name$="balance"]').val();
		var type = $('input[name$="card-type"]').val();
		// This query will determine the appropriate price to charge for a card of the value in balance
		// TODO replace url with real file
		$.get("scripts/price?card-type=" + type + "&balance=" + balance, function(data){
				if(data > 0.0) {
					$("contract-price").html("$" + data);	
				}
			}
		);
	}
	
	function isNewCard(){
		var num = $('input[name$="card_number"]').val();
		var b_id = $('input[name$="card_number"]').val();
		$('input[name$="card_number"]').focusout(function (){
			$.get("scripts/cardnum?card_number=" + num + "&brand_id="+brand_id, function(data){
				if(data=='0'){
					$("#cardnum-message").html("<strong>This is already an AvantCard</strong>");
					$("#cardnum-checkbox").html("");
				}
				else {
					$("#cardnum-message").html("");
					$("#cardnum-checkbox").html("<img src='images/check.png' class='check'>");
				}
			}
		}
		
	}
	
	function isValidPassword(){
	}
	
	function passwordsMatch(){
	}
	
	
	function submitButton(){ // use with the form onsubmit function
//		$('form[name$="balance"]').click(function(){
//		if(form_complete){
//				$.get("buy-avant-card?brand_id=" + brandID + "&card_value=" + balance
//			} form action
//		});
		return (value_complete && brand_complete);
	}
	/*
	var g_username = 0;
	var g_email = 0;
	var g_password = 0;
	var g_password2 = 0;
	var g_name = 0;
	function checkForm(){
		var username = $("#username").val();  
		var email = $("#email").val(); 
		var password = $("#password").val(); 
		var full_name = $("#name").val();  
		if(username.length > 0 && email.length > 0 && password.length >0 && full_name.length > 0){   
			var dataString = 'username='+ username + '&email=' + email + '&full_name=' + full_name +'&password='+password;       
			$.ajax({  
			  type: "POST",  
			  url: "php-scripts/register.php",  
			  data: dataString,  
			  success: function(data) {  
			    	if(data == 1){
				    	document.location = "/dashboard";
					}       
					else{
					   
					}                     
			  }  
			});
		}
	}
	function checkSmallForm(){
		var username = $("#username").val();  
		var email = $("#email").val(); 
		var password = $("#password").val();  
		var full_name = "xxxxx";  
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;  
		if(username.length > 0 && email.length > 0 && password.length >0 && reg.test(email) != false){
			var dataString = 'username='+ username + '&email=' + email + '&full_name=' + full_name +'&password='+password;        
			$.ajax({  
			  type: "POST",  
			  url: "php-scripts/register-small.php",  
			  data: dataString,  
			  success: function(data) {  
			    	if(data != 0){
						//now we have username/id so...
						var arr = data.split("/");
						username = arr[0];
						written_id = arr[1];   
						sendTrait();
						$("#send-as").fadeIn("slow");
						$("#send-as").html("<div id='highlight'>You are logged in. <a href='/dashboard'>Click here to see your dashboard.</a></div>");
						$("#chars-count").fadeIn("slow");
						$("#get-users").fadeOut("fast");             
						
					}  
					else{
					   
					}
			  }  
			});
		}
	}
	function putCheckmarkNoSpace(div){
		$(document).ready(function(){ 
			if(div == "username"){
				var username = $("#username").val();
				if(username.length > 0){
					$.get("php-scripts/check_username.php?username="+username, function(data){
						if(data == "1"){
							$(".username").html("<img src='images/check.png' class='check'>Username"); 
							$("#username-error").html(""); 
							g_username = 1;
							
						}                                                                                   
						else{
							$(".username").html("<img src='images/x.png' alt='That username is already taken.' class='check'>Username"); 
							$("#username-error").html("Sorry, that username is taken."); 
							g_username = 0;
						}
					}); 
				}else{
					$(".username").html("<img src='images/x.png' alt='Please enter a username.' class='check'>Username"); 
					$("#username-error").html("Please enter a username.");
					g_username = 0;
				}   
			}
			else if(div == "username-small"){
				var username = $("#username").val();
				if(username.length > 0){
					$.get("php-scripts/check_username.php?username="+username, function(data){
						if(data == "1"){
							$(".username-small").html("<img src='images/check.png' class='check'>Username"); 
							$("#username-error").html(""); 
							g_username = 1;
							
						}                                                                                   
						else{
							$(".username-small").html("<img src='images/x.png' alt='That username is already taken.' class='check'>Username"); 
							$("#username-error").html("Sorry, that username is taken."); 
							g_username = 0;
						}
					}); 
				}else{
					$(".username-small").html("<img src='images/x.png' alt='Please enter a username.' class='check'>Username"); 
					$("#username-error").html("Please enter a username.");
					g_username = 0;
				}   
			}
			else if( div == "email"){
				var email = $("#email").val();
				if(email.length > 1){
					var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				   	if(reg.test(email) == false) {
				      	$(".email").html("<img src='images/x.png' class='check'>Email");
						$("#email-error").html("Sorry, that email is not valid."); 
						g_email = 0;
				   	}
					else{
						$.get("php-scripts/check_email.php?email="+email,function(data){
							if (data =="1"){
									$(".email").html("<img src='images/check.png' class='check'>Email"); 
									$("#email-error").html("");    
									g_email = 1;
							}
							else{
								$(".email").html("<img src='images/x.png' class='check'>Email");
								$("#email-error").html("Sorry, that email is already taken."); 
								g_email = 0;									
							}
						});
					}   
				}
				      
			}   
			else if(div == "password"){
				var password = $("#password").val();
				if(password.length > 3){
					$(".password").html("<img src='images/check.png' class='check'>Password");
					$("#password-error").html("");
					g_password = 1;       
				}                                                                                        
				else{
					$(".password").html("<img src='images/x.png' class='check'>Password");
					$("#password-error").html("Sorry, your password must be longer than 3 characters.");
					g_password = 0;  
				}
				
			}   
			else if(div == "password2"){ 
				var password = $("#password").val(); 
				var password2 = $("#password2").val();
				if(password == password2){
					$(".password2").html("<img src='images/check.png' class='check'>Confirm password");
					$("#password2-error").html("");  
					g_password2 = 1; 
				}
				else{
					$(".password2").html("<img src='images/x.png' class='check'>Confirm password");
					$("#password2-error").html("Sorry, your password must be longer than 3 characters."); 
					g_password2 = 0; 
				}
				       
			}   
			else if(div == "name"){
				var name = $("#name").val();
				if(name.length > 1){
					$(".name").html("<img src='images/check.png' class='check'>Full name");
					$("#name-error").html(""); 
					g_name= 1;  
				}  
				else{
				   	$(".name").html("<img src='images/x.png' class='check'>Full name");
					$("#name-error").html("Please include your full name.");  
					g_name = 0; 
				}
				      
			}
		});
	}
    function putCheckmark(div){
		$(document).ready(function(){ 
			if(div == "username"){
				var username = $("#username").val();
				if(username.length > 0){
					$.get("php-scripts/check_username.php?username="+username, function(data){
						if(data == "1"){
							$(".username").html("<img src='images/check.png' class='check'>Username<br>"); 
							$("#username-error").html(""); 
							g_username = 1;
							
						}                                                                                   
						else{
							$(".username").html("<img src='images/x.png' alt='That username is already taken.' class='check'>Username<br>"); 
							$("#username-error").html("Sorry, that username is taken."); 
							g_username = 0;
						}
					}); 
				}else{
					$(".username").html("<img src='images/x.png' alt='Please enter a username.' class='check'>Username<br>"); 
					$("#username-error").html("Please enter a username.");
					g_username = 0;
				}   
			}
			else if(div == "username-small"){
				var username = $("#username").val();
				if(username.length > 0){
					$.get("php-scripts/check_username.php?username="+username, function(data){
						if(data == "1"){
							$(".username-small").html("<img src='images/check.png' class='check'>Username<br>"); 
							$("#username-error").html(""); 
							g_username = 1;
							
						}                                                                                   
						else{
							$(".username-small").html("<img src='images/x.png' alt='That username is already taken.' class='check'>Username<br>"); 
							$("#username-error").html("Sorry, that username is taken."); 
							g_username = 0;
						}
					}); 
				}else{
					$(".username-small").html("<img src='images/x.png' alt='Please enter a username.' class='check'>Username<br>"); 
					$("#username-error").html("Please enter a username.");
					g_username = 0;
				}   
			}
			else if( div == "email"){
				var email = $("#email").val();
				if(email.length > 1){
					var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				   	if(reg.test(email) == false) {
				      	$(".email").html("<img src='images/x.png' class='check'>Email<br>");
						$("#email-error").html("Sorry, that email is not valid."); 
						g_email = 0;
				   	}
					else{
						$.get("php-scripts/check_email.php?email="+email,function(data){
							if (data =="1"){
									$(".email").html("<img src='images/check.png' class='check'>Email<br>"); 
									$("#email-error").html("");    
									g_email = 1;
							}
							else{
								$(".email").html("<img src='images/x.png' class='check'>Email<br>");
								$("#email-error").html("Sorry, that email is already taken."); 
								g_email = 0;									
							}
						});
					}   
				}
				      
			}   
			else if(div == "password"){
				var password = $("#password").val();
				if(password.length > 3){
					$(".password").html("<img src='images/check.png' class='check'>Password<br>");
					$("#password-error").html("");
					g_password = 1;       
				}                                                                                        
				else{
					$(".password").html("<img src='images/x.png' class='check'>Password<br>");
					$("#password-error").html("Sorry, your password must be longer than 3 characters.");
					g_password = 0;  
				}
				
			}   
			else if(div == "password2"){ 
				var password = $("#password").val(); 
				var password2 = $("#password2").val();
				if(password == password2){
					$(".password2").html("<img src='images/check.png' class='check'>Confirm password<br>");
					$("#password2-error").html("");  
					g_password2 = 1; 
				}
				else{
					$(".password2").html("<img src='images/x.png' class='check'>Confirm password<br>");
					$("#password2-error").html("Sorry, your password must be longer than 3 characters."); 
					g_password2 = 0; 
				}
				       
			}   
			else if(div == "name"){
				var name = $("#name").val();
				if(name.length > 1){
					$(".name").html("<img src='images/check.png' class='check'>Full name<br>");
					$("#name-error").html(""); 
					g_name= 1;  
				}  
				else{
				   	$(".name").html("<img src='images/x.png' class='check'>Full name<br>");
					$("#name-error").html("Please include your full name.");  
					g_name = 0; 
				}
				      
			}
		});
	}   */     