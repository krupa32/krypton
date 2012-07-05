/*
 * index.js
 * Krupa Sivakumaran, Jul 4 2012
 */

var app = {
	
	init: function() {
		
		$('#resume').kfilepicker();
		
		$('#experience').kslider({
			max: 50
		});
		
		$('#apply').click(app.upload_resume);

	},
	
	upload_resume: function() {
		//debugger;
		var fd = new FormData();
		var resume_file = $('#resume').kfilepicker('option', 'file_data');
		
		console.log('upload_resume');
		fd.append('resume', resume_file);
		fd.append('email', $('#email').val());
		fd.append('experience', $('#experience').kslider('option', 'value'));

		console.log('calling ajax. email=' + $('#email').val() + " exp=" + $('#experience').kslider('option', 'value'));
		$.ajax({
			url: 'upload_resume.php',
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			success: function(resp){
				console.log('remote_upload received: ' + resp);
			},
		});
		
		return false;
	}
};