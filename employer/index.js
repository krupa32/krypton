
var app = {
	
	init: function() {
		
		$('button#login').click(function() {
			
			var param = {};
			
			param.email = $('#email').val();
			param.password = $('#password').val();
			
			$.post('/employer/login.php', param, function(data) {
				var resp = JSON.parse(data);
				console.log('login received: ' + resp);
				
				if (resp === true) {
					window.location.href = "/app/";
				} else {
					alert('Login failed. Please try again.');
				}
			});
			
			return false;
		});
		
	}
};