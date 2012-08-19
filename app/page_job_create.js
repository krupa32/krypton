
var page_job_create = {
	
	// stores the page to return to
	back_page: null,
	
	init: function() {
		console.log('initializing page_job_create');
		
		$('#experience').kslider({ max:50 });
		$('#ctc').kslider({ max:5000000, step:100000 });
		
		$('#page_job_create #back').click(function(){
			if (page_job_create.back_page) {
				$('#page_job_create').hide();
				page_job_create.back_page.show();
			}
		});
		
		$('#page_job_create #create').click(function(){
			
			// collect params
			var params = {};
			params.title = $('#page_job_create #title').val();
			params.tags = $('#page_job_create #tags').val();
			params.location = $('#page_job_create #location').val();
			params.experience = $('#page_job_create #experience').kslider('option', 'value');
			params.ctc = $('#page_job_create #ctc').kslider('option', 'value');
			params.description = $('#page_job_create #description').val();
			
			// create the job
			$.post('job_create.php', params, function(data){
				var resp = JSON.parse(data);
				console.log('job_create received: ' + resp);
				if (resp !== true) {
						alert("There was an error creating the job. [" + resp + "]");
						return;
				}
				
				// return to back page
				$('#page_job_create #back').click();
				
			});
		});
	},
	
	show: function(back_page) {
		this.back_page = back_page;
		$('#page_job_create').show();
	}
};

/* register with the application */
app.register_page('page_job_create', page_job_create);