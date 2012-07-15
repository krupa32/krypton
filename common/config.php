<?php
	$db_host = "localhost";
	$db_user = "root";
	$db_password = "fossil27";
	$db_name = "app_db";
	
	/* all resume uploads are stored in /uploads.
	 * this dir should be created with write permission for the apache user/group (www-data)
	 */
	$upload_dir = "/uploads";
	$upload_txt_dir = "/uploads_txt";
	
	$client_sock_name = "/tmp/client.sock";
	$indexer_sock_name = "/tmp/indexer.sock";
	
?>