
var page_job_details = {
	
	cur_job_id: null,
	
	init: function() {
		console.log('initializing page_job_details');
		
		
		$('td.applications').droppable({
			
			hoverClass: 'hover',
			
			activate: function(event, ui) {
				var status = ui.draggable.data('status');
				
				$('td.applications').droppable('disable');
				if (status == '1') // new
					$('td#schedule, td#rejected').droppable('enable');
				else if (status == '2') // schedule
					$('td#interview, td#rejected').droppable('enable');
				else if (status == '3') // interview
					$('td#schedule, td#offered, td#rejected').droppable('enable');
				else if (status == '4') // offered
					$('td#selected, td#rejected').droppable('enable');
				else if (status == '5') // rejected
					$('td#schedule, td#offered').droppable('enable');
				else if (status == '6') // selected
					$('td#rejected').droppable('enable');
			},
			
			deactivate: function(event, ui) {
				$('td.applications').droppable('enable');
			},
			
			drop: function(event, ui) {
				var drop_id, params = {}, drop_target;
				
				drop_target = $(this);
				drop_id = $(this).attr('id');
				
				params.candidate_id = ui.draggable.data('candidate_id');
				params.job_id = ui.draggable.data('job_id');
				params.score = ui.draggable.data('score');
				params.status = page_job_details.id_to_status(drop_id);
				
				if (ui.draggable.data('status') == 1) { // new application is moved
					console.log('creating app for ' + params.candidate_id + ' and status ' + params.status);
					$.post('app_create.php', params, function(data){
						var resp = JSON.parse(data), i;
						console.log('app_create received: ' + data);
						if (typeof resp == 'string') {
								alert("There was an error updating the app status. [" + resp + "]");
								return;
						}

						ui.draggable.data('status', params.status);
						ui.draggable.data('app_id', resp)
						ui.draggable.detach().appendTo(drop_target).css('left', 'auto').css('top', 'auto');
						
						page_job_details.refresh_new_apps();
						
						page_comment_add.show(ui.draggable.data('app_id'));
						
					});
					
				} else { // existing application is moved
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

						page_comment_add.show(ui.draggable.data('app_id'));
						
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
		else if (id == 'selected')
			status = 6;
		
		return status;
	},

	status_to_id: function(status) {
		var id = null;

		if (status == '1')
			id = 'new';
		else if (status == '2')
			id = 'schedule';
		else if (status == '3')
			id = 'interview';
		else if (status == '4')
			id = 'offered';
		else if (status == '5')
			id = 'rejected';
		else if (status == '6')
			id = 'selected';
		
		return id;
	},
	
	refresh_new_apps: function() {
		
		var params = {}, i;
		params.id = this.cur_job_id;
		
		console.log('getting new applications');
		$.post('app_get_new.php', params, function(data) {
			var resp = JSON.parse(data), i, name, score, div;
			console.log('new_applications received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the job details. [" + resp + "]");
					return;
			}

			if (resp.results == null) // no more new apps found
				return;
			
			// remove all applications in ui and add the received data
			$('table#applications #new').html('');
			for (i = 0; i < resp.results.length; i++) {
				name = resp.results[i].name;
				score = resp.results[i].score;
				div = $('<div class="application"><h5>' + name + '</h5><h6>Score: ' + score + '</h6></div>');
				div.data('candidate_id', resp.results[i].id);
				div.data('app_id', null);
				div.data('job_id', page_job_details.cur_job_id);
				div.data('score', score);
				div.data('status', '1'); // new
				div.find('h5').click(function() {
					console.log('app clicked');
					div = $(this).parent();
					app.show_page('page_application', { app_id: div.data('app_id'), candidate_id: div.data('candidate_id') });
				});
				div.draggable({ revert: 'invalid' });
				$('table#applications #new').append(div);
			}
		});
		
	},
	
	show: function(job_id) {

		$('#page_job_details').show();

		if (!job_id) // probably due to back button, nothing to load
			return;
		
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
		
		this.refresh_new_apps();
		
		// get currently active applications
		$.post('app_get_active.php', params, function(data) {
			var resp = JSON.parse(data), i, name, score, div, id, candidate_id, app_id;
			console.log('app_get_active received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the job details. [" + resp + "]");
					return;
			}

			if (resp == null) // no active apps found
				return;
			
			// remove all applications in ui
			$('td#schedule, td#interview, td#offered, td#rejected, td#selected').html('');

			// add the active applications back			
			for (i = 0; i < resp.length; i++) {
				name = resp[i].name;
				score = resp[i].score;
				app_id = resp[i].id;
				candidate_id = resp[i].candidate_id;
				div = $('<div class="application"><h5>' + name + '</h5><h6>Score: ' + score + '</h6></div>');
				div.data('app_id', resp[i].id);
				div.data('candidate_id', resp[i].candidate_id);
				div.data('job_id', page_job_details.cur_job_id);
				div.data('score', score);
				div.data('status', resp[i].status);
				div.draggable({ revert: 'invalid' });
				div.children('h5').click(function() {
					console.log('app clicked');
					div = $(this).parent();
					app.show_page('page_application', { app_id: div.data('app_id'), candidate_id: div.data('candidate_id') });
				});
				id = page_job_details.status_to_id(resp[i].status);
				$('td#' + id).append(div);
			}
		});
		
	},
	
	back: function() {
		app.show_page('page_jobs');
	},
	
	show_comment_form: function() {
	}
};

/* register with the application */
app.register_page('page_job_details', page_job_details);