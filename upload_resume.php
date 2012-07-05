<?php
	
	include "common/config.php";

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$email = $_POST["email"];
	$experience = $_POST["experience"];
	
	$q = "insert into candidates set email='$email', experience=$experience";
	if (!$db->query($q)) {
		$ret = "Error creating candidate record";
		goto out;
	}
	
	$id = $db->insert_id;
	
	/* all resume uploads are stored in /uploads.
	 * this dir should be created with write permission for the apache user/group (www-data)
	 */
	$upload_dir = "/uploads";
	
	/* check if upload dir exists */
	if (!is_dir($upload_dir)) {
		$ret = "/uploads does not exist";
		goto out;
	}
	
	if ($_FILES["resume"]["error"] != UPLOAD_ERR_OK) {
		$ret = "Error uploading";
		goto out;
	}
	
	$src = $_FILES["resume"]["tmp_name"];
	$dst = "$upload_dir/$id.doc";
	error_log("moving [$src] to [$dst]");
	move_uploaded_file($src, $dst);
	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
