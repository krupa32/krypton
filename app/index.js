
var app = {
	
	/* list of pages in the application */
	pages: { },
	
	register_page: function(pg_name, pg) {
		console.log('registering page:' + pg_name);
		this.pages[pg_name] = pg;
	},
	
	init: function() {
		
		var pg_name;
		
		for (pg_name in this.pages)
			this.pages[pg_name].init();
		
		$('#header a').click(function(){
			var pg_name = $(this).attr('pg_name');
			$('.page').hide();
			app.pages[pg_name].show();
			return false;
		});
		
		$('.page').hide();
		this.pages['page_msgs'].show();
	}
};