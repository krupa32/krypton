<?php
	
	include "../common/config.php";
	include "../common/utils.php";

	function get_recruiter_name_from_id($db, $id)
	{
		$ret = null;
		
		$q = "select email from recruiters where id=$id;";
		$res = $db->query($q);
		if ($res && $res->num_rows > 0) {
			$row = $res->fetch_assoc();
			$ret = $row["email"];
			$res->close();
		}
		
		return $ret;
	}
	
	session_start();

	$app_id = $_POST["app_id"];
	$candidate_id = $_POST["candidate_id"];
	
	$ret = null;
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}
	
	/* get the candidate details */
	$q = "select * from candidates where id=$candidate_id;";
	$res = $db->query($q);
	if (!$res) {
		$ret = $db->error;
		goto err;
	}
	if ($res->num_rows == 0) {
		$ret = "Candidate not found";
		goto err;
	}
	
	$row = $res->fetch_assoc();
	$ret["name"] = $row["name"];
	$ret["location"] = $row["location"];
	$ret["ctc"] = $row["min_ctc"];
	$ret["experience"] = $row["experience"];
	
	$res->close();
	
	/* get the resume */
	$resume = file_get_contents("/uploads_txt/$candidate_id.txt");
	$ret["resume"] = preg_replace("/\n/", "<br>", $resume);
	
	if ($app_id != "null") {
		/* get the app details */
		$q = "select * from applications where id=$app_id;";
		$res = $db->query($q);
		if (!$res) {
			$ret = $db->error;
			goto err;
		}

		if ($res->num_rows == 0) {
			$ret = "Candidate not found";
			goto err;
		}
		
		$row = $res->fetch_assoc();
		$ret["rating"] = $row["rating"];
		$ret["status"] = $row["status"];
		
		$res->close();
		
		/* get the comments */
		$ret["comments"] = array();
		$q = "select commentor_id, UNIX_TIMESTAMP(commented_on) as time, comment from comments where application_id=$app_id;";
		$res = $db->query($q);
		if (!$res) {
			$ret = $db->error;
			goto err;
		}

		while ($row = $res->fetch_assoc()) {
			$comment["commentor"] = get_recruiter_name_from_id($db, $row["commentor_id"]);
			$comment["time"] = relative_date($row["time"]);
			$comment["comment"] = $row["comment"];
			$ret["comments"][] = $comment;
		}
		
		$res->close();
		
	}
	

err:
	if (!$db->connect_error)
		$db->close();
		
	print json_encode($ret);
?>
