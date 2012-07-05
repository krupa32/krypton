/*
 * kslider.js
 * Krupa Sivakumaran, Jul 1 2012
 */

(function($){
	
	var methods = {
		
		init: function(options) {
			
			var elem = this.get(0);
			
			this.addClass('kslider');
			
			this.append('<span>0</span>');
			
			// add a slide event to the options
			options = $.extend(options, {
				slide: function(event, ui){
					$(elem).children('span').html(ui.value);
				}
			});
			
			this.slider(options);
		},
		
		option: function(name, new_val) {
			return this.slider('option', name, new_val);
		}
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