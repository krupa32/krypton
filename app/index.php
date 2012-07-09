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
				<ul>
					<li>HOME</li>
					<li>JOBS</li>
					<li>APPLICATIONS</li>
					<li>INTERVIEWS</li>
				</ul>
			</div>
		</div>
		
		<div id="search">
			<div class="center">
				<div class="page" id="home">
					Home page
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