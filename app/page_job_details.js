
var page_job_details = {
	
	init: function() {
		console.log('initializing page_job_details');
	},
	
	show: function(job_id) {
		
		var params = {};
		params.id = job_id;
		
		console.log('getting job details');
		$.post('job_details.php', params, function(data){
			var resp = JSON.parse(data), i;
			console.log('job_details received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the job details. [" + resp + "]");
					return;
			}
			
			$('#page_job_details #title').html(resp.title);
			$('#page_job_details #team_loc').html(resp.team + ', ' + resp.location);
			$('#page_job_details #experience').html(resp.experience + ' yrs experience in');
			$('#page_job_details #tags').html(resp.tags);
		});
		
		$('#page_job_details').show();
	},
	
	back: function() {
		app.show_page('page_jobs');
	}
};

/* register with the application */
app.register_page('page_job_details', page_job_details);