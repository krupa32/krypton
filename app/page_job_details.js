
var page_job_details = {
	
	cur_job_id: null,
	
	init: function() {
		console.log('initializing page_job_details');
		
		
		$('td.applications').droppable({
			
			hoverClass: 'hover',
			
			drop: function(event, ui) {
				var drop_id, params = {}, drop_target;
				
				drop_target = $(this);
				drop_id = $(this).attr('id');
				
				params.candidate_id = ui.draggable.data('candidate_id');
				params.job_id = ui.draggable.data('job_id');
				params.score = ui.draggable.data('score');
				params.status = page_job_details.id_to_status(drop_id);
				
				if (ui.draggable.data('status') == 1) { // this is new application
					console.log('creating app for ' + params.candidate_id + ' and status ' + params.status);
					$.post('app_create.php', params, function(data){
						var resp = JSON.parse(data), i;
						console.log('app_create received: ' + data);
						if (typeof resp == 'string') {
								alert("There was an error updating the app status. [" + resp + "]");
								return;
						}

						ui.draggable.data('status', params.status);
						ui.draggable.detach().appendTo(drop_target).css('left', 'auto').css('top', 'auto');
						
					});
					
				} else { // this is already existing application
					console.log('updating app ' + params.candidate_id + ' to ' + params.status);
					$.post('app_update_status.php', params, function(data){
						var resp = JSON.parse(data), i;
						console.log('app_update_status received: ' + data);
						if (typeof resp == 'string') {
								alert("There was an error updating the app status. [" + resp + "]");
								return;
						}

						ui.draggable.data('status', params.status);
						ui.draggable.detach().appendTo(drop_target).css('left', 'auto').css('top', 'auto');
						
					});
				}
				
				
			}
		});
	},
	
	id_to_status: function(id) {
		var status = null;
		if (id == 'new')
			status = 1;
		else if (id == 'schedule')
			status = 2;
		else if (id == 'interview')
			status = 3;
		else if (id == 'offered')
			status = 4;
		else if (id == 'rejected')
			status = 5;
		
		return status;
	},
	
	show: function(job_id) {
		
		this.cur_job_id = job_id;
		
		var params = {};
		params.id = job_id;
		
		// get job details
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
		
		// get new applications
		console.log('getting new applications');
		$.post('new_applications.php', params, function(data) {
			var resp = JSON.parse(data), i, name, score, div;
			console.log('new_applications received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the job details. [" + resp + "]");
					return;
			}

			// remove all applications in ui and add the received data
			$('table#applications #new').html('');
			for (i = 0; i < resp.results.length; i++) {
				name = resp.results[i].name;
				score = resp.results[i].score;
				div = $('<div class="application"><h5>' + name + '</h5><h6>Score: ' + score + '</h6></div>');
				div.data('candidate_id', resp.results[i].id);
				div.data('job_id', job_id);
				div.data('score', score);
				div.data('status', 1); // new
				div.draggable({ revert: 'invalid' });
				$('table#applications #new').append(div);
			}
		});
		
		// get currently active applications
		
		$('#page_job_details').show();
	},
	
	back: function() {
		app.show_page('page_jobs');
	}
};

/* register with the application */
app.register_page('page_job_details', page_job_details);