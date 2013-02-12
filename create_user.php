<?php
	include("config.php");
	
	$email="dws2890@aol.com";
	$pass="cats";
	$fname="David";
	$lname="Smith";
	
	$hash = hash('sha256', $pass);
	
	function createSalt()
	{
		$string = md5(uniqid(rand(), true));
		return substr($string, 0, 3);
	}
	
	$salt = createSalt();
	$hash = hash('sha256', $salt . $hash);

	$cxn = mysqli_connect("$host", "$user", "$password", "$database") or die ("Query died: connect");
	
	$email = mysqli_real_escape_string($cxn, $email);
	$fname = mysqli_real_escape_string($cxn, $fname);
	$lname = mysqli_real_escape_string($cxn, $lname);

	$sql = "INSERT INTO tbl_users (email, password, salt, fname, lname) VALUES ('$email', '$hash', '$salt','$fname','$lname');";
	mysqli_query($cxn,$sql) or die("Couldn't execute query");
	mysqli_close($cxn);
	
	echo "success!";
?>
	