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

require("config.php"); //require file for MySQL database info

// Connect to MySQL database
$cxn=mysqli_connect("$host", "$user", "$password","$database") or die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error()); 

//store MySQL thread ID for later
$thread_id=mysqli_thread_id($cxn);

if($_POST['Lhouse_lights'] != NULL)
{	
	$lhl=$_POST['Lhouse_lights'];
	$sql="UPDATE tbl_actions SET UserL_Loff=$lhl";
	mysqli_query($cxn,$sql) or die("Couldn't execute this query");
}

if($_POST['Lhouse_locks'] != NULL)
{	
	$lhlock=$_POST['Lhouse_locks'];
	$sql="UPDATE tbl_actions SET UserL_Lock=$lhlock";
	mysqli_query($cxn,$sql) or die("Couldn't execute this query");
}

$sql="SELECT room_id FROM tbl_rooms";
$result=mysqli_query($cxn,$sql) or die("Couldn't execute query");

while($rows = mysqli_fetch_assoc($result)) 
{	
	$room_id=$rows['room_id'];
	$enter_upd = $_POST['enter_'.$room_id];
	$leave_upd = $_POST['leave_'.$room_id];
	if($enter_upd != NULL)
	{	
		$sql="UPDATE tbl_actions SET Loff_UserE_Lon=$enter_upd WHERE room_id=$room_id";
		mysqli_query($cxn,$sql) or die("Couldn't execute this query");
	}
	if($leave_upd != NULL)
	{	
		$sql="UPDATE tbl_actions SET Lon_UserL_Loff=$leave_upd WHERE room_id=$room_id";
		mysqli_query($cxn,$sql) or die("Couldn't execute this query");
	}
}
//kill and close the mysql connection
mysqli_kill($cxn,$thread_id);
mysqli_close($cxn);

header("Location: view_home.php");//redirect user to the view_home page


?>