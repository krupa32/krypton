<?php
	
	$upload_dir = "/uploads/";
	//error_log("upload dir is " . $upload_dir);
	
	/* check if upload dir exists */
	if (!is_dir($upload_dir)) {
		$ret = "/uploads does not exist";
		goto out;
	}
		
	if ($_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
		$src = $_FILES["resume"]["tmp_name"];
		$dst = $upload_dir . $_FILES["resume"]["name"];
		error_log("moving [$src] to [$dst]");
		move_uploaded_file($src, $dst);
		$ret = true;
	} else {
		error_log("Error uploading");
		$ret = false;
	}

out:
	print json_encode($ret);
?>
