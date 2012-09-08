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

		<script type="text/javascript" src="/app/page_jobs.js"></script>
		<script type="text/javascript" src="/app/page_job_create.js"></script>
		<script type="text/javascript" src="/app/page_job_details.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				app.init();
			});
		</script>
	</head>
	
	<body>
		<div id="top_bar">
			<div class="left"><button id="back">Back</button></div>
			<span>Welcome <?php print $_SESSION["user_email"]; ?></span>
			<a href="">HOME</a>
			<a href="">SETTINGS</a>
			<a href="">USERS</a>
			<a href="">HELP</a>
			<a href="">LOGOUT</a>
		</div>
		
		<div id="content">
			
			<div class="page" id="page_jobs">
				<div id="toolbar_jobs">
					<div class="left"><button id="btn_create_job">Create New</button></div>
					<div class="clear"></div>
					<table id="table_jobs"></table>
				</div>
			</div>
			
			<div class="page" id="page_job_create">
				<table cellspacing=20>
					<tr><td class="form_label">Title</td><td><input type="text" id="title"/></td></tr>
					<tr><td class="form_label">Team</td><td><input type="text" id="team"/></td></tr>
					<tr><td>Tags</td><td><input type="text" id="tags"/></td></tr>
					<tr><td>Location</td><td><input type="text" id="location" /></td></tr>
					<tr><td>Experience</td><td><div id="experience"></div></td></tr>
					<tr><td>Max CTC</td><td><div id="ctc"></div></td></tr>
					<tr><td>Description</td><td><textarea id="description">Enter job description here</textarea></td></tr>
					<tr><td>&nbsp;</td><td><button id="create">Create</button></td></tr>
				</table>
			</div>
			
			<div class="page" id="page_job_details">
			
				<table id="job_details">
					<tr>
						<td class="applications"></td>
						<td class="summary"><h1 id="title">Job Title</h1><h2 id="team_loc"></h2></td>
						<td class="details"><h2 id="experience">2 yrs experience in</h2><h2 id="tags"></h2></td>
						<td class="actions">
							<button id="edit">Edit Job Details</button><br><br>
							<button id="cancel">Cancel Job</button><br><br>
						</td>
					</tr>
				</table>
				
				<table id="applications">
					<tr>
						<td class="header">TO BE SCREENED</td>
						<td class="header">TO BE SCHEDULED</td>
						<td class="header">TO BE INTERVIEWED</td>
						<td class="header">OFFERED</td>
						<td class="header">REJECTED</td>
					</tr>
					<tr>
						<td class="applications">
							<div class="application to_be_screened">
								<h5>Krupa Sivakumaran</h5>
								<h6>Score: 556</h6>
							</div>
						</td>
						<td class="applications"></td>
						<td class="applications"></td>
						<td class="applications"></td>
						<td class="applications"></td>
					</tr>
				</table>
				
			</div>
		</div>
	</body>
</html>