
var page_comment_add = {
	
	/* current application id to which the comment is added */
	app_id: null,
	
	init: function() {
		console.log('initializing page_comment_add');
		
		$('#page_comment_add #add').click(function() {
			var params = {};
			params.app_id = page_comment_add.app_id;
			params.comment = $('#page_comment_add #comment').val();
			
			// console.log('Adding comment ' + params.app_id + ' ' + params.comment);
			$.post('comment_add.php', params, function(data) {
				var resp = JSON.parse(data);
				console.log('comment_add received: ' + data);
				if (typeof resp == 'string') {
						alert("There was an error adding the comment. [" + resp + "]");
						return;
				}

				$('#page_comment_add').dialog('close');

			});
			
		});

		$('#page_comment_add #later').click(function() {
			$('#page_comment_add').dialog('close');
		});

	},
	
	show: function(app_id) {
		
		this.app_id = app_id;
		
		$('#page_comment_add').dialog({
			width: 'auto',
			modal: true
		});
	}
};

/* register with the application */
app.register_page('page_comment_add', page_comment_add);