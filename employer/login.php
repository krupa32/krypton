<?php
	
	include "../common/config.php";
	
	session_start();

	$email = $_POST["email"];
	$password = $_POST["password"];

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$q = "select id,company_id,password from recruiters where email='$email'";
	$res = $db->query($q);
	if (!$res || $res->num_rows == 0) {
		$ret = "Could not find $email in db";
		goto out;
	}
	
	$row = $res->fetch_assoc();
	
	/* check password */
	if (crypt($password, $row["password"]) != $row["password"]) {
		$ret = "Password mismatch";
		goto out;
	}
	
	/* store the user id in the session */
	$_SESSION["user_id"] = $row["id"];
	$_SESSION["company_id"] = $row["company_id"];
	
	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
