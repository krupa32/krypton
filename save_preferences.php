<?php
	
	include "common/config.php";
	
	session_start();

	$id = $_SESSION["user_id"];
	$location = $_POST["location"];
	$ctc = $_POST["ctc"];

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$q = "update candidates set location='$location', min_ctc=$ctc where id=$id";
	$db->query($q);
	
	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
