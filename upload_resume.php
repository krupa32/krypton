<?php
	
	include "common/config.php";
	include "common/utils.php";
	
	session_start();

	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto out;
	}
	
	$email = $_POST["email"];
	$experience = $_POST["experience"];
	$name = $_POST["name"];
	
	/* check if this user already exists in our db */
	$q = "select id from candidates where email='$email'";
	if (!($result = $db->query($q))) {
		$ret = "Error checking if user exists";
		goto out;
	}
	
	if ($result->num_rows == 0) {
		/* user does not exist, create a new record */
		$q = "insert into candidates set email='$email', name='$name', experience=$experience";
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
		
		$q = "update candidates set name='$name', experience=$experience where id=$id";
		$db->query($q);
	}
	
	/* save the user's id in the session.
	 * it will be used in case the user updates preferences.
	 */
	$_SESSION["user_id"] = $id;
	
	
	/* check if upload dir exists */
	if (!is_dir($upload_dir)) {
		$ret = "/uploads does not exist";
		goto out;
	}
	
	if ($_FILES["resume"]["error"] != UPLOAD_ERR_OK) {
		$ret = "Error uploading";
		goto out;
	}
	
	/* move uploaded resume to upload dir */
	$src = $_FILES["resume"]["tmp_name"];
	$dst = "$upload_dir/$id.doc";
	error_log("moving [$src] to [$dst]");
	move_uploaded_file($src, $dst);

	/* check if upload txt dir exists */	
	if (!is_dir($upload_txt_dir)) {
		$ret = "/uploads does not exist";
		goto out;
	}

	/* convert resume to txt and save in upload txt dir */
	system("abiword --to=$upload_txt_dir/$id.txt $dst", $ret);
	if ($ret != 0) {
		$ret = "Error converting resume to txt";
		goto out;
	}

	/* send parse cmd to indexer */
	$cmd = pack("iii", 0x01, 4, $id);
	$rsp_data = indexer_exec($cmd, 12, 12);
	$rsp = unpack("iopcode/ilen/istatus", $rsp_data);
	if ($rsp["status"] != 0) {
		$ret = "Error indexing txt file";
		goto out;
	}
	

	$ret = true;

out:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
