<?php
	session_start(); //start the session
	
	$_SESSION = array(); //destroy all of the session variables
	session_destroy(); //destroy the session 
	
	header("Location: index.php"); //redirect to login page
?>
