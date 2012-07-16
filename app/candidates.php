<?php
	
	include "../common/config.php";
	include "../common/utils.php";
	
	
	function filter_old($tags, $experience, $ctc, $location)
	{
		global $db_host, $db_user, $db_password, $db_name;
		
		$db = new mysqli($db_host, $db_user, $db_password, $db_name);
		if ($db->connect_error) {
			$ret = "Error opening db";
			goto err;
		}
		
		$q = "select * from candidates";
		$res = $db->query($q);
		if (!$res || $res->num_rows == 0) {
			$ret = false;
			goto err;
		}
		
		while ($row = $res->fetch_assoc())
			$ret[] = $row;

err:
		if (!$db->connect_error)
			$db->close();

		return $ret;
	}
	

	function filter($tags, $experience, $ctc, $location)
	{
		global $db_host, $db_user, $db_password, $db_name;
		$ret = null;
		
		$db = new mysqli($db_host, $db_user, $db_password, $db_name);
		if ($db->connect_error) {
			$ret = "Error opening db";
			goto err;
		}

		/* send match cmd to indexer */
		$cmd = pack("iia100iia100", 0x02, 208, $tags, $experience, $ctc, $location);
		$rsp_data = indexer_exec($cmd, 216, 8 + 804);
		$rsp = unpack("iopcode/ilen/in_matches", $rsp_data);
		error_log("n_matches = " . $rsp["n_matches"]);
		
		for ($i = 0; $i < $rsp["n_matches"]; $i++) 
		{
			$info_str = substr($rsp_data, 12 + ($i * 8));
			$info = unpack("iid/iscore", $info_str);
			$q = "select * from candidates where id=" . $info["id"];
			$res = $db->query($q);
			if ($res && $res->num_rows > 0 && ($row = $res->fetch_assoc())) {
				$row["score"] = $info["score"];
				$ret[] = $row;
			}
		}

		/* execute the stub function for now */
		//return filter_old($tags, $experience, $ctc, $location);
		
err:
		if (!$db->connect_error)
			$db->close();
		
		return $ret;
	}


	session_start();

	$id = $_SESSION["user_id"];
	$tags = $_POST["tags"];
	$experience = $_POST["experience"];
	$ctc = $_POST["ctc"];
	$location = $_POST["location"];
	$start = $_POST["start"];
	$count = $_POST["count"];
	$ret["results"] = null;
	
	$start_time = microtime(true);
	
	$matches = filter($tags, $experience, $ctc, $location);
	if (!$matches)
		goto out;
	
	for ($i = $start; $i < $start + $count && $i < count($matches); $i++)
		$ret["results"][] = $matches[$i];


out:
	$end_time = microtime(true);
	$ret["search_time"] = ($end_time - $start_time) * 1000; // in ms
		
	print json_encode($ret);
?>
