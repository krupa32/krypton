<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$id = $_POST["id"];
	
	$ret = null;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "select * from jobs where id=$id;";
	$res = $db->query($q);
	if (!$res) {
		$ret = $db->error;
		goto err;
	}
	if ($res->num_rows == 0) {
		$ret = "Job not found";
		goto err;
	}
	
	while ($row = $res->fetch_assoc()) {
		$ret = $row;
	}
	
	$res->close();

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
