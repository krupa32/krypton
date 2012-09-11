<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$job_id = $_POST["job_id"];
	$candidate_id = $_POST["candidate_id"];
	$status = $_POST["status"];
	
	$ret = true;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "update applications set status=$status where job_id=$job_id and candidate_id=$candidate_id";
	if (!$db->query($q)) {
		$ret = $db->error;
		goto err;
	}

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
