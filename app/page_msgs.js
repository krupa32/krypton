
var page_msgs = {
	
	init: function() {
		console.log('initializing page_home');
	},
	
	show: function() {
		$('#page_msgs').show();
	}
	
};

/* register with the application */
app.register_page('page_msgs', page_msgs);

/* For future reference.
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
			console.log('refresh received: ' + data);
			var resp = JSON.parse(data);
			// fill the received data in the page
			var tbl = $('#home #results');
			var _tbl = tbl.get(0), i, j, _row, _cells;
			
			tbl.html('');
			
			var res = resp.results;
			if (!res)
				return;
			
			$('#home #search_time').html(resp.search_time.toFixed(2));
			
			for (i = 0; i < res.length; i++) {
				_row = _tbl.insertRow(-1);
				for (j = 0; j < 5; j++)
					_row.insertCell(-1);
				
				_cells = _row.cells;
				
				$(_cells[0]).addClass('name').append('<h1>' + res[i].name + '</h1><p class="hint">Updated on ' + res[i].updated_on + '</p>');
				$(_cells[1]).addClass('score').append('<p class="hint">' + res[i].score + '</p>');
				$(_cells[2]).addClass('experience').append('<p>' + res[i].experience + '</p><p class="hint">Yrs Experienced</p>');
				$(_cells[3]).addClass('ctc').append('<p>' + res[i].min_ctc + '</p><p class="hint">Expected CTC</p>');
				$(_cells[4]).addClass('location').append('<p>' + res[i].location + '</p><p class="hint">Preferred Location</p>');
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
*/