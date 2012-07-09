
var app = {
	
	init: function() {
		
		$('#create_company').click(function() {
			
			var param = {};
			param.email = $('#create_company_section #email').val();
			param.mobile = $('#create_company_section #mobile').val();
			param.name = $('#create_company_section #name').val();
			param.address = $('#create_company_section #address').val();
			
			$.post('/admin/company_create.php', param, function(data) {
				var resp = JSON.parse(data);
				console.log('Received: ' + resp);
				
				if (resp === true) {
					console.log('success');
				} else {
					alert('Operation failed. Check console log.');
				}
			});
			
			return false;
		});

		$('#create_recruiter').click(function() {
			
			var param = {};
			param.company_id = $('#create_recruiter_section #company_id').val();
			param.email = $('#create_recruiter_section #email').val();
			param.password = $('#create_recruiter_section #password').val();
			
			$.post('/admin/recruiter_create.php', param, function(data) {
				var resp = JSON.parse(data);
				console.log('Received: ' + resp);
				
				if (resp === true) {
					console.log('success');
				} else {
					alert('Operation failed. Check console log.');
				}
			});
			
			return false;
		});
		
	}
};