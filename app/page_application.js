
var page_application = {
	
	init: function() {
		console.log('initializing page_application');
		
	},
	
	
	show: function(params) {

		$('#page_application').show();
		
		console.log('showing application:' + params.app_id + ' candidate:' + params.candidate_id);
		
		$.post('app_get_details.php', params, function(data) {
			var resp = JSON.parse(data), page, i, tbl, row, cell;
			console.log('app_get_details received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the app details. [" + resp + "]");
					return;
			}
			
			page = $('#page_application');
			
			page.find('#name').html(resp.name);
			page.find('#rating').html('Rating:' + resp.rating);
			page.find('#experience').html(resp.experience + 'years experience');
			page.find('#location').html('Prefers to work in ' + resp.location);
			page.find('#ctc').html('Expects a salary of Rs.' + resp.ctc + ' per annum');
			page.find('#resume').html(resp.resume);
			
			tbl = page.find('table#comments').get(0);
			tbl.innerHTML = '';
			for (i = 0; i < resp.comments.length; i++) {
				row = tbl.insertRow(-1);
				
				cell = row.insertCell(-1);
				cell.className = 'time';
				cell.innerHTML = '<p>' + resp.comments[i].commentor + '</p><p>' + resp.comments[i].time + '</p>';
				
				cell = row.insertCell(-1);
				cell.className = 'comment';
				cell.innerHTML = '<p>' + resp.comments[i].comment + '</p>';
			}

		});
	},
	
	back: function() {
		app.show_page('page_job_details');
	}
	
};

/* register with the application */
app.register_page('page_application', page_application);