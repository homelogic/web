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

?>

<html> <title>View Home</title>
<body>

<link type="text/css" rel="stylesheet" href="homelogic.css">
<img src="images/HomeLogicLogo.jpg" alt="Home Logic Logo" class="logo"/>

<?php
require("config.php"); //require file for MySQL database info

// Connect to MySQL database
$cxn=mysqli_connect("$host", "$user", "$password","$database") or die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error()); 

//store MySQL thread ID for later
$thread_id=mysqli_thread_id($cxn);

//query database for tbl_device table and order by rooms
$get_table="SELECT * FROM tbl_device ORDER BY tbl_device.room ASC LIMIT 0, 30"; 
$result=mysqli_query($cxn,$get_table) or die("Couldn't execute this query");

//create table and its headers
echo '<center><table border="1" cellpadding="4" class="devices">'; 
echo '<tr><th>Device Name</th><th>Type</th><th>Room Name</th><th>Status</th>	
		<th>Value</th><th>Update Status to: </th><th>Update Value to: </th></tr>';

//begin form for status and value updates and on submit redirect to update_devices.php
echo '<form name="Update_device" action="update_devices.php" method="post">';

//while loop that runs for each row of the tbl_device table
while($rows = mysqli_fetch_assoc($result)) 
{ 
	//set variables for the information in the table
	$ID = $rows['device_id']; 
	$Device_Name = $rows['device_name']; 
	$Type = $rows['device_type']; 
	$Room_ID = $rows['room']; 
	$Value = $rows['device_value'];
	
	//query the database to get the row in the tbl_rooms table where the "room_id"
	//is equal to the room of the current device then set a variable for the room name of
	//the current device
	$get_rm_name = "SELECT room_name FROM tbl_rooms WHERE room_id='$Room_ID'";
	$result1 = mysqli_query($cxn,$get_rm_name) or die("Couldn't execute that query");
	$room_row = mysqli_fetch_assoc($result1);
	$Room_Name = $room_row['room_name'];
	
	if(!$Value)//set value variable to NULL if value is 0
		$Value = NULL;
	
	//if status of device is 1
	if($rows['status'] == 1)
	{
		if($Type == 'Door Lock')//and type is door lock
			$Status = "Locked"; //then set status variable to locked
		else //status of device is 1 and type isn't door lock 
			$Status = "On";//then set status variable to On
	}
	else//status of device is 0
	{
		if($Type == 'Door Lock')//and type is door lock
			$Status = "Unlocked";//set status variable to unlocked
		else//status of device is 0 and type is not door lock
			$Status = "Off";//set status variable to off
	}	
//put information for device into the columns of the current row
echo
	'<tr><td>'.$Device_Name.'</td><td>'.$Type.'</td><td>'.$Room_Name.'</td><td>
	'.$Status.'</td><td>'.$Value.'</td>';
	
//if device is a door lock then set the options of the dropdown menu for the desired
//status to locked and unlocked. Otherwise set the options to on and off. The name of the
//input is determined by the device_id with "status_" in front of it to distinguish it as
//a status input and what device it corresponds to
if($Type !='Door Lock'){
	echo '<td><select name="status_'.$ID.'"><option value='.NULL.'>N/A</option><option value=1>On</option>
		<option value=0>Off</option></select></td>';}
else{
	echo '<td><select name="status_'.$ID.'"><option value='.NULL.'>N/A</option><option value=1>Locked</option>
		<option value=0>Unlocked</option></select></td>';}
//if the value of the device is not 0 then create an input field for the desired value
//the name of the input is determined by the device_id with "value_" in front of it to 
//distinguish it as a value input and what device it corresponds to
if($Value)
	echo '<td><input type="text" name="value_'.$ID.'"/></td></tr>'; 
else
	echo '<td></td></tr>';
}

echo'</table></center>';
//create an "Update Devices" button to submit desired updates
echo '<center><input type="submit" value="Update Devices"></center> 
</form>';
	
//create form to update actions settings
echo'<form name="Set_Actions" action="action_settings.php" method="post">';

echo '<legend>House Settings</legend>';	
echo 'When the user leaves the house do you want to turn off all the lights?';
echo '<select name="Lhouse_lights"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br>';	
		
echo 'When the user leaves the house do you want to lock all the doors?';
echo '<select name="Lhouse_locks"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br><br>';
	
$sql="SELECT * FROM tbl_rooms";
$result2=mysqli_query($cxn, $sql) or die("Couldn't execute that query");
while($rows1 = mysqli_fetch_assoc($result2))
{
	$room_id = $rows1['room_id'];
	$room_name = $rows1['room_name'];
	echo '<legend>For the '."$room_name".' which of the following actions should be taken?</legend>';
	echo 'When lights are off and user enters the room, turn on the lights?';
	echo '<select name="enter_'.$room_id.'"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br>';
	echo 'When lights are on and user leaves the room, turn off the lights?';
	echo '<select name="leave_'.$room_id.'"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br><br>';	
}
	
	//kill and close the MySQL connection
	mysqli_kill($cxn,$thread_id); 
	mysqli_close($cxn);

?>
<!--create button to set room action settings -->

<center><input type="submit" value="Save Action Settings"></center> 
</form>

<!-- create a logout button that redirects to logout.php when pressed -->
<form name="Logout" action="logout.php" method="post"> 
<center><input type="submit" value="Logout"></center> 
</form>

</body> 
</html>