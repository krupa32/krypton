<?php
	
	include "../common/config.php";
	
	session_start();

	$id = $_SESSION["user_id"];
	$location = $_POST["location"];
	$ctc = $_POST["ctc"];
	$start = $_POST["start"];
	$count = $_POST["count"];

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$q = "select * from candidates limit $start, $count;";
	$res = $db->query($q);
	if (!$res || $res->num_rows == 0) {
		$ret = false;
		goto out;
	}
	
	while ($row = $res->fetch_assoc())
		$ret[] = $row;
	

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
