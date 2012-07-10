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

		<script type="text/javascript" src="/app/page_home.js"></script>
		<script type="text/javascript" src="/app/page_jobs.js"></script>
		<script type="text/javascript" src="/app/page_applications.js"></script>
		<script type="text/javascript" src="/app/page_interviews.js"></script>
		
		<script type="text/javascript" src="/app/index.js"></script>
		<link rel="stylesheet/less" type="text/css" href="/app/index.css"></link>
		<script type="text/javascript" src="/less/less.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				app.init();
			});
		</script>
	</head>
	
	<body>
		<div id="top_bar">
			<div class="center">
				<ul>
					<li><a href="">HELP</a></li>
					<li><a href="">LOGOUT</a></li>
					<li><a href="">CONTACT US</a></li>
				</ul>
				<p>Welcome <?php print $_SESSION["user_email"]; ?></p>
			</div>
		</div>
		<div id="header">
			<div class="center">
				<img src="/images/logo.png" />
				<div class="nav" id="nav_home">INTERVIEWS</div>
				<div class="nav" id="nav_home">APPLICATIONS</div>
				<div class="nav" id="nav_home">JOBS</div>
				<div class="nav" id="nav_home">HOME</div>
			</div>
		</div>
		
		<div id="search">
			<div class="center">
			
				<div class="page" id="home">
					<div class="filter" id="filter_home">
						<p>Tags<br><input type="text" id="tags"></p>
						<div id="location_div">Location<br><input type="text" id="location"></div>
						<div id="ctc_div">Maximum CTC<br><div id="max_ctc"></div></div>
						<div id="experience_div">Minimum Experience<br><div id="min_experience"></div></div>
						<div class="clear"></div>
					</div>
					<table id="results">
						
					</table>
				</div>
				
				<div class="page" id="jobs">
					Jobs page
				</div>
				<div class="page" id="applications">
					Applications page
				</div>
				<div class="page" id="interviews">
					Interviews page
				</div>
			</div>
		</div>
	</body>
</html>