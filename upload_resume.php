<?php
	
	include "common/config.php";

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$email = $_POST["email"];
	$experience = $_POST["experience"];
	
	/* check if this user already exists in our db */
	$q = "select id from candidates where email='$email'";
	if (!($result = $db->query($q))) {
		$ret = "Error checking if user exists";
		goto out;
	}
	
	if ($result->num_rows == 0) {
		/* user does not exist, create a new record */
		$q = "insert into candidates set email='$email', experience=$experience";
		if (!$db->query($q)) {
			$ret = "Error creating candidate record";
			goto out;
		}
		/* get the generated id */
		$id = $db->insert_id;
	} else {
		/* user already found in db, update the experience and use the id */
		$row = $result->fetch_assoc();
		$id = $row["id"];
		error_log("User $email found. Using id $id");
		
		$q = "update candidates set experience=$experience where id=$id";
		$db->query($q);
	}
	

	
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
