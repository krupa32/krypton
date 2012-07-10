
var page_home = {
	
	init: function() {
		console.log('initializing page_home');
		
		$('#min_experience').kslider({ max:50 });
		
		$('#max_ctc').kslider({ max:5000000, step:50000 });
	},
	
	show: function() {
		$('#home').show();
		this.refresh();
	},
	
	refresh: function() {
		console.log('refreshing home page');
		
		var param = {}, val;
		if ((val = $('#tags').val())) param.tags = val;
		if ((val = $('#location').val())) param.location = val;
		if ((val = $('#min_experience').kslider('option', 'value'))) param.min_experience = val;
		if ((val = $('#max_ctc').kslider('option', 'value'))) param.max_ctc = val;
		
		console.log('sending ajax: tags=' + param.tags + ', loc=' + param.loc + ', exp=' + param.min_experience + ', ctc=' + param.max_ctc);
		$.post('/app/candidates.php', param, function(data){
			var resp = JSON.parse(data);
			console.log('refresh received: ' + resp);
			//debugger;
			// fill the received data in the page
		});
		
	}
};