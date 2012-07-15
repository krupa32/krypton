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
		/* send match cmd to indexer */
		$cmd = pack("iia100iia100", 0x02, 208, $tags, $experience, $ctc, $location);
		$rsp_data = indexer_exec($cmd, 216, 12);
		$rsp = unpack("iopcode/ilen/istatus", $rsp_data);
		if ($rsp["status"] != 0) {
			$ret = "Error finding matches";
			goto err;
		}

		/* execute the stub function for now */
		return filter_old($tags, $experience, $ctc, $location);
		
err:
		return null;
	}

	session_start();

	$id = $_SESSION["user_id"];
	$tags = $_POST["tags"];
	$experience = $_POST["experience"];
	$ctc = $_POST["ctc"];
	$location = $_POST["location"];
	$start = $_POST["start"];
	$count = $_POST["count"];
	
	$matches = filter($tags, $experience, $ctc, $location);
	if (!$matches)
		goto out;
	
	for ($i = $start; $i < $start + $count && $i < count($matches); $i++)
		$ret[] = $matches[$i];


out:
		
	print json_encode($ret);
?>
