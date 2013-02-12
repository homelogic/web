<?php


session_start(); //start the session

require("config.php"); //require the file containing the MySQL database information

//connect to the MySQL database
$cxn = mysqli_connect("$host", "$user", "$password", "$database") or die ("Query died: connect");

switch (@$_POST['Button'])
{
  case "Log in": //when the user clicks the Login submit button run this code
	
	//save the email and password supplied by the user into variables
	$login_email = $_POST['login_email'];
	$login_password = $_POST['login_password'];
	
	
	//change the user_email string to an escaped SQL string for the database connection
	//so it can be used in an SQL statement
	$login_email = mysqli_real_escape_string($cxn, $login_email);
	
	//query the database to get the password and salt string from tbl_users table
	//where the email matches the one provided
	$sql = "SELECT password, salt FROM tbl_users WHERE email='$login_email';";
	$result = mysqli_query($cxn,$sql) or die("Query died: password");
	$num = mysqli_num_rows($result); //count the number of rows the query returned	
	
	if($num < 1) // if no rows were returned by the query then the email doesn't exist
	{	
		//let the error message be that the email doesn't exist and then reload login form
		$message_1="The Email you entered does not exist! Please try again.";
		include("login_form.inc");
	}	
	else //a row with the given email address exists
	{	
		//fetch the user data in the result
		$userData = mysqli_fetch_assoc($result);
		
		//create encrypted password from the salt string retrieved from the database and 
		//the password given by the user
		$hash = hash('sha256', $userData['salt'] . hash('sha256', $login_password));
		
		if($hash != $userData['password'])
		{	//if the password created from the salt string and the password given by the
			//user does not match the password in the MySQL database then let the error
			//message be that the password is incorrect, reset user_email variable and 
			//reload login form
			$message_1="The Email, '$login_email' exists, but you have not entered the correct password! Please try again.";
			$user_email=strip_tags(trim($_POST['login_email']));
			include("login_form.inc");			
		}
		else// passwords match
		{	
			//create new session id and keep variables for security reasons
			//then redirect user to view_home.php
			session_regenerate_id();
			$_SESSION['auth']= 1;
			header("Location: view_home.php");
		}
	}
  break;
  
  case "Register": 
  	$reg_email = $_POST['reg_email'];
  	$reg_password = $_POST['reg_password'];
  	$pw_confirm = $_POST['pw_confirm'];
  	$user_fname = $_POST['user_fname'];
  	$user_lname = $_POST['user_lname'];
  	
  	if($reg_password != $pw_confirm)
  	{
  		$message_1="The passwords entered do not match!";
  		include("login_form.inc");
  	}
  	else
  	{
  	$hash = hash('sha256', $reg_password);
	
	function createSalt()
	{
		$string = md5(uniqid(rand(), true));
		return substr($string, 0, 3);
	}
	
	$salt = createSalt();
	$hash = hash('sha256', $salt . $hash);

	$cxn = mysqli_connect("$host", "$user", "$password", "$database") or die ("Query died: connect");
	
	$reg_email = mysqli_real_escape_string($cxn, $reg_email);
	$user_fname = mysqli_real_escape_string($cxn, $user_fname);
	$user_lname = mysqli_real_escape_string($cxn, $user_lname);

	$sql = "INSERT INTO tbl_users (email, password, salt, fname, lname) VALUES ('$reg_email', '$hash', '$salt','$user_fname','$user_lname');";
	mysqli_query($cxn,$sql) or die("Couldn't execute query");
	
	$message_1="You have been successfully registered! Please Login.";
	include("login_form.inc");
	}
  break;
  
  default://use the login form as a default for the switch statement
    include("login_form.inc");
}
?>