<?php
$url=$_SERVER['REQUEST_URI'];
header("Refresh: 30; URL=$url"); 

session_start();//start the session
ini_set('session.gc-maxlifetime', 600);
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
echo '<tr><th>Device Name</th><th>Type</th><th>Room</th><th>Status</th><th>Update Status</th>	
		<th>Current Temp</th><th>Value/Temp. Settings</th><th>Update Value/Temp. Settings</th></tr>';

//while loop that runs for each row of the tbl_device table
while($rows = mysqli_fetch_assoc($result)) 
{ 
	//set variables for the information in the table
	$ID = $rows['device_id']; 
	$Device_Name = $rows['device_name']; 
	$Type = $rows['device_type']; 
	$Room_ID = $rows['room']; 
	$Value = $rows['device_value'];
	$current_temp = $rows['current_temp'];
	
	//query the database to get the row in the tbl_rooms table where the "room_id"
	//is equal to the room of the current device then set a variable for the room name of
	//the current device
	$get_rm_name = "SELECT room_name FROM tbl_rooms WHERE room_id='$Room_ID'";
	$result1 = mysqli_query($cxn,$get_rm_name) or die("Couldn't execute that query");
	$room_row = mysqli_fetch_assoc($result1);
	$Room_Name = $room_row['room_name'];
		
	//if status of device is 1
	if($rows['status'] == 1)
	{
		if($Type == 'Door Lock'){//and type is door lock
			$Status = "Locked"; //then set status variable to locked
			$change_status="Unlock Door"; }
		else{ //status of device is 1 and type isn't door lock 
			$Status = "On";//then set status variable to On
			$change_status="Turn Off";}
			
	}
	else//status of device is 0
	{
		if($Type == 'Door Lock'){//and type is door lock
			$Status = "Unlocked";//set status variable to unlocked
			$change_status="Lock Door";}
		else{//status of device is 0 and type is not door lock
			$Status = "Off";//set status variable to off
			$change_status = "Turn On";}
	}	
	//put information for device into the columns of the current row
	echo
	'<tr><td>'.$Device_Name.'</td><td>'.$Type.'</td><td>'.$Room_Name.'</td><td>
	'.$Status.'</td>';
	
	//create form for each device to update the status
	echo '<form name="status_'.$ID.'" action="update_status.php" method="post">';
	echo "<input type='hidden' name='device' value='$ID'>";
	if($change_status=="Turn Off" || $change_status=="Unlock Door")
	{
		echo '<input type="hidden" name="status" value=0>';
		echo "<td><input type='submit' value='$change_status'></form></td>";
	}
	else
	{	
		echo '<input type="hidden" name="status" value=1>';
		echo "<td><input type='submit' value='$change_status'></form></td>";
	}
	
	if(!$current_temp)//put nothing in table column for current_temp if $current_temp is 0 or NULL
		echo '<td></td>';
	else	
		echo '<td>'.$current_temp.'</td>';
	
	if(!$Value)//put nothing in table column for value if $value is 0 or null
		echo '<td></td><td></td></tr>';
	else {	
		echo '<td>'.$Value.'/'.($Value+4).'</td>';
		echo '<form name="value_'.$ID.'" action="update_value.php" method="post">';
		echo "<input type='hidden' name='device' value='$ID'>";
		echo '<td><input type="text" name="value" required>';
		echo '<input type="submit" value="Update"></form></td></tr>'; }
}

echo'</table></center>';
	
//create form to update actions settings
echo'<form name="Set_Actions" action="action_settings.php" method="post">';

echo '<center><p><legend>House Settings</legend>';	
echo 'Turn off all lights when all users leave house?';
echo '<select name="Lhouse_lights"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br>';	
		
echo 'Lock all doors when all users leave house?';
echo '<select name="Lhouse_locks"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br></p></center>';
	
$sql="SELECT * FROM tbl_rooms";
$result2=mysqli_query($cxn, $sql) or die("Couldn't execute that query");
while($rows1 = mysqli_fetch_assoc($result2))
{
	$room_id = $rows1['room_id'];
	$room_name = $rows1['room_name'];
	echo '<center><p><legend>'."$room_name".' Settings</legend>';
	echo 'Turn on lights when user enters the room?';
	echo '<select name="enter_'.$room_id.'"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br>';
	echo 'Turn off lights when user leaves the room?';
	echo '<select name="leave_'.$room_id.'"><option value='.NULL.'>N/A</option><option value=1>Yes</option>
		<option value=0>No</option></select><br></p></center>';	
}
	
//kill and close the MySQL connection
mysqli_kill($cxn,$thread_id); 
mysqli_close($cxn);

?>
<!--create button to set room action settings -->

<center><input type="submit" value="Save Action Settings"></center></form>

<!-- create a logout button that redirects to logout.php when pressed -->
<form name="Logout" action="logout.php" method="post"> 
<center><input type="image" src="images/logout.jpg" alt="Logout Button"></center> </form>

</body> 
</html>