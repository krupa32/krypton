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
		<script type="text/javascript" src="/app/page_job_create.js"></script>
		
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
					<!--<a href="" id="a_msgs" pg_name="page_msgs">MESSAGES</a>
					<a href="" id="a_jobs" pg_name="page_jobs">JOBS</a>-->
					<button id="btn_msgs" pg_name="page_msgs">MESSAGES</button>
					<button id="btn_jobs" pg_name="page_jobs">JOBS</button>
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
					<div>Messages page</div>
				</div>
				
				<div class="page" id="page_jobs">
					<div id="toolbar_jobs">
						<div class="left"><button id="btn_create_job">Create New</button></div>
						<div class="right">
							<span>Showing </span>
							<input type="radio" id="radio_all_jobs" name="radio_jobs"> All Jobs
							<input type="radio" id="radio_my_jobs" name="radio_jobs"> My Jobs
						</div>
						<div class="clear"></div>
					</div>
				</div>
				
				<div class="page" id="page_job_create">
					<div id="toolbar_job_create">
						<button id="back">Back</button>
					</div>
					<table cellspacing=20>
						<tr><td class="form_label">Title</td><td><input type="text" id="title"/></td></tr>
						<tr><td>Tags</td><td><input type="text" id="tags"/></td></tr>
						<tr><td>Location</td><td><input type="text" id="location" /></td></tr>
						<tr><td>Experience</td><td><div id="experience"></div></td></tr>
						<tr><td>Max CTC</td><td><div id="ctc"></div></td></tr>
						<tr><td>Description</td><td><textarea id="description">Enter job description here</textarea></td></tr>
						<tr><td>&nbsp;</td><td><button id="create">Create</button></td></tr>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>