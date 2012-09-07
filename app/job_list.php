<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$company_id = $_SESSION["company_id"];
	$user_id = $_SESSION["user_id"];
	$start = $_POST["start"];
	$count = $_POST["count"];
	
	$ret = array();
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "select id, title, team, created_on, tags from jobs where owner_id=$user_id;";
	$res = $db->query($q);
	if (!$res) {
		$ret = $db->error;
		goto err;
	}
	
	while ($row = $res->fetch_assoc()) {
		$ret[] = $row;
	}
	
	$res->close();

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
