(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
		
        elementorFrontend.hooks.addAction('frontend/element_ready/tripgo_elementor_ova_image_gallery.default', function(){
	       
	        /* Add your code here */
	    	$('.ova-image-gallery-ft').each(function(){
	    		var that  	= $(this);
	    		var item 	= that.find('.item-fancybox-ft');

	    		var fancybox_ops = that.data('options') ? that.data('options') : {};

	    		item.on('click', function(){
	    			
	    			Fancybox.bind('[data-fancybox="image-gallery-ft"]', {
					 	infinite: fancybox_ops.loop,
					});

					
	    		});
	    	});
        });

   });

})(jQuery);
