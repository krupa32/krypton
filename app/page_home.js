
var page_home = {
	
	/* start record number to fetch */
	start: 0,
	
	/* num records to fetch */
	count: 10,
	
	/* time (in ms) to wait after a filter changes and before refresh */
	refresh_timeout: 3000,
	
	/* refresh timer handle */
	refresh_timer: null,
	
	init: function() {
		console.log('initializing page_home');
		
		$('#min_experience').kslider({ max:50 });
		
		$('#max_ctc').kslider({ max:5000000, step:50000 });
		
		$('input#tags').keyup(this, this.filter_changed);
		$('input#location').keyup(this, this.filter_changed);
		$('#min_experience').bind('slidechange', this, this.filter_changed);
		$('#max_ctc').bind('slidechange', this, this.filter_changed);
	},
	
	show: function() {
		$('#home').show();
		this.refresh();
	},
	
	refresh: function() {
		console.log('refreshing home page');
		
		this.start = 0;
		
		var param = {}, val;
		if ((val = $('#tags').val())) param.tags = val;
		if ((val = $('#location').val())) param.location = val;
		if ((val = $('#min_experience').kslider('option', 'value'))) param.experience = val;
		if ((val = $('#max_ctc').kslider('option', 'value'))) param.ctc = val;
		param.start = this.start;
		param.count = this.count;
		
		console.log('sending ajax: tags=' + param.tags + ', loc=' + param.location + ', exp=' + param.experience + ', ctc=' + param.ctc);
		$.post('/app/candidates.php', param, function(data){
			var resp = JSON.parse(data);
			console.log('refresh received: ' + resp);
			//debugger;
			// fill the received data in the page
			var tbl = $('#home #results');
			var _tbl = tbl.get(0), i, j, _row, _cells;
			
			tbl.html('');
			for (i = 0; i < resp.length; i++) {
				_row = _tbl.insertRow(-1);
				for (j = 0; j < 5; j++)
					_row.insertCell(-1);
				
				_cells = _row.cells;
				
				$(_cells[0]).addClass('name').append('<h1>' + resp[i].name + '</h1><p class="hint">Updated on ' + resp[i].updated_on + '</p>');
				$(_cells[1]).addClass('score').append('<p class="hint">' + 'N/A' + '</p>');
				$(_cells[2]).addClass('experience').append('<p>' + resp[i].experience + '</p><p class="hint">Yrs Experienced</p>');
				$(_cells[3]).addClass('ctc').append('<p>' + resp[i].min_ctc + '</p><p class="hint">Expected CTC</p>');
				$(_cells[4]).addClass('location').append('<p>' + resp[i].location + '</p><p class="hint">Preferred Location</p>');
			}
		});
		
	},

	filter_changed: function(event) {
		var pg = event.data;
		
		console.log('restarting refresh timer');
		if (pg.refresh_timer)
			clearTimeout(pg.refresh_timer);
			
		pg.refresh_timer = setTimeout(function(){
			console.log('refresh_timer expired');
			pg.refresh_timer = null;
			pg.refresh();
		}, pg.refresh_timeout);
	}
};