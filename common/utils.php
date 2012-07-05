<?php

	function logged_in()
	{
		session_start();
	
		if (!$_SESSION["user_id"])
			return false;
		else
			return true;
	}
	
	
	function relative_date($date)
	{
		$now = new DateTime();
		$dt = new DateTime($date);
		$diff = $now->diff($dt);
	
		if ($diff->d)
			$ret = $diff->format("%d days ago");
		else if ($diff->h)
			$ret = $diff->format("%h hours ago");
		else if ($diff->m)
			$ret = $diff->format("%m minutes ago");
		else
			$ret = "Just now";
	
		return $ret;
	}
?>
