<?php
	
	include "../common/config.php";
	
	session_start();

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$company_id = $_POST["company_id"];
	$email = $_POST["email"];
	$password = crypt($_POST["password"]);
	
	$q = "insert into recruiters set company_id=$company_id, email='$email', password='$password'";
	if (!$db->query($q)) {
		$ret = "Error creating recruiter record: " . $db->error;
		goto out;
	}

	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
