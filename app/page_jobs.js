
var page_jobs = {
	
	init: function() {
		console.log('initializing page_jobs');
		
		$('#btn_create_job').click(function(){
			$('.page').hide();
			page_job_create.show(page_jobs);
		});
	},
	
	show: function() {
		$('#page_jobs').show();
	}
};

/* register with the application */
app.register_page('page_jobs', page_jobs);