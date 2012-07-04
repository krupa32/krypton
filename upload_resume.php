<?php
	
	$upload_dir = $_SERVER["DOCUMENT_ROOT"] . "/uploads";
	
	/* check if upload dir exists */
	if (!is_dir($upload_dir))
		mkdir($upload_dir);
		
	if ($_FILES["resume"]["error"] == UPLOAD_ERR_OK) {
		$src = $_FILES["resume"]["tmp_name"];
		$dst = "/uploads/" . $_FILES["resume"]["name"];
		error_log("moving [$src] to [$dst]");
		move_uploaded_file($src, $dst);
		$ret = true;
	} else {
		error_log("Error uploading");
		$ret = false;
	}

	print json_encode($ret);
?>
