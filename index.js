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
		
		$('#back').click(function(){
			$('#form_preferences').hide('slide', { direction: 'right' }, 'fast', function(){
				$('#form_resume').show('slide', { direction: 'left' });
			});
			return false;
		})

	},
	
	upload_resume: function() {

		var fd = new FormData();
		var resume_file = $('#resume').kfilepicker('option', 'file_data');
		
		fd.append('resume', resume_file);
		fd.append('email', $('#email').val());
		fd.append('experience', $('#experience').kslider('option', 'value'));

		$.ajax({
			url: 'upload_resume.php',
			type: 'POST',
			data: fd,
			processData: false,
			contentType: false,
			success: function(data){
				resp = JSON.parse(data);
				console.log('remote_upload received: ' + resp);
				if (resp !== true) {
					alert("There was an error uploading your resume. [" + resp + "]");
					return;
				}
				$('#form_resume').hide('slide', { direction: 'left' }, 'fast', function(){
					$('#form_preferences').show('slide', { direction: 'right' });
				});
			},
		});
		
		return false;
	}
};