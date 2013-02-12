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

//query database for the "tbl_device" table
$get_table="SELECT device_id FROM tbl_device";
$result=mysqli_query($cxn,$get_table) or die("Couldn't execute query");

//while loop that runs for for each row in the tbl_device table
while($rows = mysqli_fetch_assoc($result)) 
{ 
	//store the device ID of the device in the current row
	$ID = $rows['device_id']; 
	
	//retrieve the desired status update for the current device
	$status_upd = $_POST['status_'.$ID];
	
if($status_upd != NULL)//if the device needs to be updated
{
	//query the database to update the status of the device to what is desired
	$sql1 = "UPDATE tbl_device SET status=$status_upd WHERE device_id='$ID'";
	mysqli_query($cxn,$sql1) or die("Couldn't execute query");
}
if($value_id = $_POST['value_'.$ID])//if the value needs to be updated
{	
	//set a variable to the integer value of the desired device value and query the 
	//database to update the value of the device to that variable
	$value= intval($value_id);
	$sql2 = "UPDATE tbl_device SET device_value='$value' WHERE device_id='$ID'";	
	mysqli_query($cxn,$sql2) or die("Couldn't execute query");	
}	
}
//kill and close the mysql connection
mysqli_kill($cxn,$thread_id);
mysqli_close($cxn);

header("Location: view_home.php");//redirect user to the view_home page

?>
