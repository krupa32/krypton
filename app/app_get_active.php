<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	

	function filter($id)
	{
		global $db_host, $db_user, $db_password, $db_name;
		$ret = null;
		
		$db = new mysqli($db_host, $db_user, $db_password, $db_name);
		if ($db->connect_error) {
			$ret = "Error opening db";
			goto err;
		}
		
		$q = "select * from jobs where id=$id";
		$res = $db->query($q);
		if (!$res || $res->num_rows == 0) {
			$ret = "Job not found";
			goto err;
		}
	
		$row = $res->fetch_assoc();
		$tags = $row["tags"];
		$experience = $row["experience"];
		$ctc = $row["max_ctc"];
		$location = $row["location"];

		/* send match cmd to indexer */
		$cmd = pack("iia100iia100", 0x02, 208, $tags, $experience, $ctc, $location);
		$rsp_data = indexer_exec($cmd, 216, 8 + 804);
		$rsp = unpack("iopcode/ilen/in_matches", $rsp_data);
		error_log("n_matches = " . $rsp["n_matches"]);
		
		for ($i = 0; $i < $rsp["n_matches"]; $i++) 
		{
			$info_str = substr($rsp_data, 12 + ($i * 8));
			$info = unpack("iid/iscore", $info_str);
			
			// check if candidate has already applied for the job
			$q = "select * from applications where job_id=$id and candidate_id=" . $info["id"];
			$res = $db->query($q);
			if ($res && $res->num_rows > 0) {
				// candidate already applied for this job
				continue;
			}
			
			$q = "select * from candidates where id=" . $info["id"];
			$res = $db->query($q);
			if ($res && $res->num_rows > 0 && ($row = $res->fetch_assoc())) {
				$row["score"] = $info["score"];
				$ret[] = $row;
			}
		}

		
err:
		
		return $ret;
	}


	session_start();

	$id = $_POST["id"];

	$ret = array();

	$start_time = microtime(true);
	
	$db = new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($db->connect_error) {
		$ret = "Error opening db";
		goto err;
	}

	$q = "select * from applications where job_id=$id";
	$res = $db->query($q);
	if (!$res) {
		$ret = $db->error();
		goto err;
	}
	
	while ($row = $res->fetch_assoc()) {
		
		$candidate_id = $row["candidate_id"];
		$q = "select name from candidates where id=$candidate_id";
		$res2 = $db->query($q);
		if ($res2) {
			$row2 = $res2->fetch_assoc();
			$row["name"] = $row2["name"];
			$res2->close();
		}

		$ret[] = $row;

	}
	
	$res->close();

err:
	if (!$db->connect_error)
		$db->close();

out:
	$end_time = microtime(true);
	//$ret["search_time"] = ($end_time - $start_time) * 1000; // in ms

		
	print json_encode($ret);
?>
