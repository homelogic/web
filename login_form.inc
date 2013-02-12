<html><head>
<title>User Login Page</title>

<link type="text/css" rel="stylesheet" href="homelogic.css">

</head>
<body style="margin: 0">
<img src="images/HomeLogicLogo.jpg" alt="Home Logic Logo" class="logo"/>

<?php
	$check = "SELECT * FROM tbl_users";
	$ch_result =mysqli_query($cxn, $check);
	$num = mysqli_num_rows($ch_result); //count the number of rows the query returned 
				
if($num != 0)
{
//the following code is the script for the login page (a user has been registered)

//use following array where the keys refer to the submitted information and the values 
//refer to the login labels
$fields = array("login_email"   =>   "Email",
				"login_password"  =>   "Password" );


echo"<div id='login'>
	 <form action='".$_SERVER['PHP_SELF']."' method='post'>
	   <fieldset><legend>User Login</legend>";
  
	if(isset($message_1))//if the error message is set
	{	
		//print error message to the screen in the format for errors
		echo "<p class='errors'>$message_1</p>\n";
	}
	//loop that lasts for the # of pairs in the $fields array where $field is set to the
	//keys and $value is set to the value
	foreach($fields as $field => $value)
	{	//if the key is "login_password" then set the type of the input to password
		if($field =="login_password")
			$type = "password";
		else//other wise the type is set to text
			$type = "text";
		//create inputs and labels for the login 
		echo "<div id='field'>
			   <label for='$field'>$value</label>
			    <input id='$field' name='$field' type='$type' value='".@$$field."'
			    	size='20' maxlength='80'/></div>";
	}

		echo"<input type='submit' name='Button' style='margin-left: 45%;'
				value='Log in'/>";
	    echo"</fieldset>
		</form>
		</div>
</body>
</html>";
}
else //if a user hasn't been registered
{
	//create array where keys refer to submitted info and values refer to registration labels
	$fields= array("reg_email"  =>  "Email",
				   "reg_password" => "Password",
				   "pw_confirm" => "Confirm Password",
				   "user_fname" => "First Name",
				   "user_lname" =>  "Last Name");
	//create inputs and labels for the registration
	echo "<div id='register'><form action='".$_SERVER['PHP_SELF']."' method='post'>
	   <fieldset><legend>User Registration</legend>";
	
	if(isset($message_1))//if the error message is set
	{	
		//print error message to the screen in the format for errors
		echo "<p class='errors'>$message_1</p>\n";
	}
	//loop that lasts for the # of pairs in the $fields array where $field is set to the
	//keys and $value is set to the value
	foreach($fields as $field => $value)
	{	//set the type of input to password for password fields
		if($field == "reg_password" or $field == "pw_confirm")
			$type = "password";
		else//other wise the type is set to text
			$type = "text";
		//create inputs and labels for the login 
		echo "<div id='field'>
			   <label for='$field'>$value</label>
			    <input id='$field' name='$field' type='$type' value='".@$$field."'
			    	size='20' maxlength='80'/></div>";
	}

	echo"<input type='submit' name='Button' style='margin-left: 45%;'
				value='Register'/>";
	echo"</fieldset></form></div></body></html>	";
}
?>	