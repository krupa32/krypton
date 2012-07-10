
var app = {
	
	init: function() {
		
		page_home.init();
		page_jobs.init();
		page_applications.init();
		page_interviews.init();
		
		$('.page').hide();
		page_home.show();
	}
};