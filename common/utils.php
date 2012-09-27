<?php

	function logged_in()
	{
		session_start();
	
		if (!$_SESSION["user_id"])
			return false;
		else
			return true;
	}
	
	
	function relative_date($timestamp)
	{
		$now = new DateTime(date(DATE_W3C));
		$dt = new DateTime(date(DATE_W3C, $timestamp));
		$diff = $dt->diff($now);
	
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
	
	function indexer_exec($cmd, $cmd_len, $rsp_len)
	{
		global $client_sock_name, $indexer_sock_name;
		
		$ret = null;
		
		/* create socket */
		$cfd = socket_create(AF_UNIX, SOCK_STREAM, 0);
		if (!$cfd) {
			$ret = "Error creating socket";
			goto out;
		}
		
		/* bind socket */
		$caddr = $client_sock_name;
		unlink($caddr);
		if (!socket_bind($cfd, $caddr)) {
			$ret = "Error binding socket";
			goto out;
		}
		
		/* connect to indexer */
		$saddr = $indexer_sock_name;
		if (!socket_connect($cfd, $saddr)) {
			$ret = "Error connecting to indexer:" . socket_strerror(socket_last_error());
			goto out;
		}
		
		/* send cmd to indexer */
		if (socket_send($cfd, $cmd, $cmd_len, 0) != $cmd_len) {
			$ret = "Did not send $cmd_len bytes";
			goto out;
		}
		
		/* recv rsp from indexer */
		if (socket_recv($cfd, $rsp, $rsp_len, 0) != $rsp_len) {
			$ret = "Did not receive $rsp_len bytes";
			goto out;
		}
		
out:
		if ($cfd)
			socket_close($cfd);
		$cfd = 0;
		
		if ($ret)
			error_log($ret);
		
		return $rsp;
	}
?>
