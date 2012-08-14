<?php
	session_start();
	if (!$_SESSION["user_id"]) {
		header("Location:/employer/");
		exit(0);
	}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/jquery/jqueryui.css"></link>
		<script type="text/javascript" src="/jquery/jquery.js"></script>
		<script type="text/javascript" src="/jquery/jqueryui.js"></script>

		<link rel="stylesheet" type="text/css" href="/kslider/kslider.css"></link>
		<script type="text/javascript" src="/kslider/kslider.js"></script>

		<script type="text/javascript" src="/app/index.js"></script>
		<link rel="stylesheet/less" type="text/css" href="/app/index.css"></link>
		<script type="text/javascript" src="/less/less.js"></script>

		<script type="text/javascript" src="/app/page_msgs.js"></script>
		<script type="text/javascript" src="/app/page_jobs.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				app.init();
			});
		</script>
	</head>
	
	<body>
		<div id="top_bar">
			<div class="viewport">
				<span>Welcome <?php print $_SESSION["user_email"]; ?></span>
				<a href="">SETTINGS</a>
				<a href="">USERS</a>
				<a href="">HELP</a>
				<a href="">LOGOUT</a>
			</div>
		</div>
		<div id="header">
			<div class="viewport">
				<div class="left"><img src="/images/logoimg.png" /></div>
				<div class="center">
					<a href="" id="a_msgs" pg_name="page_msgs">MESSAGES</a>
					<a href="" id="a_jobs" pg_name="page_jobs">JOBS</a>
				</div>
				<div class="right">
					<input type="text" />
				</div>
				<div class="clear"></div>
			</div>
		</div>
		
		<div id="content">
			<div class="viewport">
			
				<div class="page" id="page_msgs">
					Messages page
				</div>
				
				<div class="page" id="page_jobs">
					Jobs page
				</div>
			</div>
		</div>
	</body>
</html>