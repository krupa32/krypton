/*
 * kslider.js
 * Krupa Sivakumaran, Jul 1 2012
 */

(function($){
	
	var methods = {
		
		init: function(options) {
			
			var elem = this.get(0);
			
			this.addClass('kslider');
			
			// add a slide event to the options
			options = $.extend(options, {
				slide: function(event, ui){
					$(elem).children('span').html(ui.value);
				}
			});
			
			this.slider(options);
		},
		

	};
	
	$.fn.kslider = function(method) {
		if (methods[method]) {
		  return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
		  return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist' );
		}    
	  
	}
    
})(jQuery);