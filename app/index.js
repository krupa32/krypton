
var app = {
	
	/* list of pages in the application */
	pages: { },
	
	/* current page that is displayed */
	cur_page: null,
	
	register_page: function(pg_name, pg) {
		console.log('registering page:' + pg_name);
		this.pages[pg_name] = pg;
	},
	
	show_page: function(pg_name, param) {
		$('.page').hide();
		this.pages[pg_name].show(param);
		this.cur_page = this.pages[pg_name];
		
		if (this.cur_page.back)
			$('button#back').show();
	},
	
	
	init: function() {
		
		var pg_name;
		
		for (pg_name in this.pages)
			this.pages[pg_name].init();

		$('button#back').click(function() {
			$(this).hide();
			if (app.cur_page.back)
				app.cur_page.back();
		});

		app.show_page('page_jobs');		
	}
};