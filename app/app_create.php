<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$job_id = $_POST["job_id"];
	$candidate_id = $_POST["candidate_id"];
	$score = $_POST["score"];
	$status = $_POST["status"];
	
	$ret = true;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "insert into applications values(NULL, $candidate_id, $job_id, $score, $status)";
	if (!$db->query($q)) {
		$ret = $db->error;
		goto err;
	}

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
