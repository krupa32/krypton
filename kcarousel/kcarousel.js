/*
 * kcarousel.js
 * Krupa Sivakumaran, Jul 1 2012
 */

(function($){
	
	var methods = {
		
		init: function() {
			var ctr = this.children('div');
			var imgs = ctr.children('img');
			
			this.addClass('kcarousel');
			ctr.addClass('kcarousel_container');
			
            ctr.data('width', this.innerWidth());
			ctr.data('n_images', imgs.length);
			ctr.css('width', imgs.length * ctr.data('width'));
            
            imgs.css('width', this.innerWidth());
            imgs.css('height', this.innerHeight());
			
			var h = setInterval(function(){
				methods.interval_handler(ctr);
			}, 3000);
            
			this.data('interval_handle', h);
		},
		
		interval_handler: function(ctr) {
			var w = ctr.data('width');
			var n_imgs = ctr.data('n_images');
			var ml = parseFloat(ctr.css('margin-left')) - w;
			
			if (ml == -n_imgs * w)
				ml = 0;
			ctr.animate({'margin-left': ml}, 'fast');
		}
	};
	
	$.fn.kcarousel = function(method) {
		if (methods[method]) {
		  return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
		  return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist' );
		}    
	  
	}
    
})(jQuery);