
var page_jobs = {
	
	n_columns: 5,
	
	init: function() {
		console.log('initializing page_jobs');
		
		$('#btn_create_job').click(function(){
			//$('.page').hide();
			$('#page_job_create').dialog({
				width:'auto',
				modal:true,
				close: function() {
					page_jobs.refresh();
				}
			});
		});
	},
	
	show: function() {
		$('.page').hide();
		this.refresh();
		$('#page_jobs').show();
	},
	
	refresh: function() {
		// get the job list
		var params = {};
		params.start = 0;
		params.count = 20;
		
		console.log('refreshing jobs list');
		$.post('job_list.php', params, function(data){
			var resp = JSON.parse(data), i;
			console.log('job_list received: ' + data);
			if (typeof resp == 'string') {
					alert("There was an error getting the job list. [" + resp + "]");
					return;
			}
			
			// delete all rows and add an empty row for formatting purposes
			var tbl = document.getElementById('table_jobs'), row, cell;
			while (tbl.rows.length)
				tbl.deleteRow(0);
			row = tbl.insertRow(-1);
			for (i = 0; i < page_jobs.n_columns; i++) {
				cell = row.insertCell(-1);
				cell.innerHTML = '&nbsp;';
				cell.className = 'empty';
			}
			
			// insert the jobs in following rows
			row = tbl.insertRow(-1);
			for (i = 0; i < resp.length; i++) {
				if (row.cells.length == page_jobs.n_columns)
					row = tbl.insertRow(-1);
				cell = row.insertCell(-1);
				cell.innerHTML = '<h5>' + resp[i].title + '</h5><h6>' + resp[i].team + '</h6>';
				cell.className = 'job';
				$(cell).data('job_id', resp[i].id).click(function() {
					app.show_page('page_job_details', $(this).data('job_id'));
				});
			}
			if (i == 0) {
				cell = row.insertCell(-1);
				cell.innerHTML = "You are lucky. No jobs found.";
			}
		});
	}
};

/* register with the application */
app.register_page('page_jobs', page_jobs);