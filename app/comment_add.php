<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$commentor_id = $_SESSION["user_id"];
	$app_id = $_POST["app_id"];
	$comment = $_POST["comment"];
	
	$ret = true;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "insert into comments values(NULL, $app_id, $commentor_id, NULL, '$comment');";
	if (!$db->query($q)) {
		$ret = $db->error;
		goto err;
	}

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
