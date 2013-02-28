<?php

session_start();//start the session

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
    // last request was more than 10 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

//if authorization variable is not set and equal to 1 then go back to login
if(!(isset($_SESSION['auth']) && $_SESSION['auth'])) 
{
	header("Location: index.php");
	die();
}

require('config.php');//require the file containing the MySQL database information

// Connect to MySQL database
$cxn=mysqli_connect("$host", "$user", "$password","$database") or die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error()); 

//store MySQL thread ID for later
$thread_id=mysqli_thread_id($cxn);

$ID=$_POST['device'];
$status=$_POST['status'];

$sql = "UPDATE tbl_device SET status=$status WHERE device_id='$ID'";
mysqli_query($cxn,$sql) or die("Couldn't execute query");

//kill and close the mysql connection
mysqli_kill($cxn,$thread_id);
mysqli_close($cxn);

header("Location: view_home.php");//redirect user to the view_home page

?>