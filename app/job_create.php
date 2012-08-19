<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	session_start();

	$owner_id = $_SESSION["user_id"];
	$title = strtolower($_POST["title"]);
	$location = strtolower($_POST["location"]);
	$tags = strtolower($_POST["tags"]);
	$experience = $_POST["experience"];
	$ctc = $_POST["ctc"];
	$desc = $_POST["description"];
	
	$ret = true;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	$q = "insert into jobs values(NULL, $owner_id, NULL, '$title', $experience, '$tags', '$location', $ctc, '$desc');";
	if (!$db->query($q)) {
		$ret = $db->error;
		goto err;
	}

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
