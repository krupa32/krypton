/*
 * kfilepicker.js
 * Krupa Sivakumaran, Jul 2 2012
 */

(function($) {

	var methods = {
		
		init: function() {
			var file = $('<input type="file" class="kfile" />');
			this.after(file);
			file.data('text', this);
			
			file.change(function(){
				var i = file.val().lastIndexOf('\\');
				var name = file.val().substring(i + 1);
				file.data('text').val(name);
			});
			
			this.focus(function(){
				file.click();
			});
		}
	};
	
	$.fn.kfilepicker = function(method) {
		if (methods[method]) {
		  return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
		  return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist' );
		}    
	}
	
})(jQuery);