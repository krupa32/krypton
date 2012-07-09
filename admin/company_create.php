<?php
	
	include "../common/config.php";
	
	session_start();

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$email = $_POST["email"];
	$mobile = $_POST["mobile"];
	$name = $_POST["name"];
	$address = $_POST["address"];
	
	$q = "insert into companies set email='$email', mobile='$mobile', name='$name', address='$address'";
	if (!$db->query($q)) {
		$ret = "Error creating company record: " . $db->error;
		goto out;
	}

	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
