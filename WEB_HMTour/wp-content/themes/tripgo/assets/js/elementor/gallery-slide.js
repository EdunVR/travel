(function($){
	"use strict";
	
	$(window).on('elementor/frontend/init', function () {
		
        elementorFrontend.hooks.addAction('frontend/element_ready/tripgo_elementor_gallery_slide.default', function(){
	       
	        $(".ova-gallery-slide .gallery-slide-carousel").each(function(){

		        var owlsl 		= $(this) ;
		        var owlsl_ops 	= owlsl.data('options') ? owlsl.data('options') : {};

		        if ( $('body').hasClass('rtl') ) {
                	owlsl_ops.rtl = true;
                }

		        var responsive_value = {
		            0:{
		              	items:1,
		              	dot:true,
		            },
		            767:{
		              	items:2,
		            },
		            1024: {
		            	items: owlsl_ops.items - 1,
		            },
		            1200: {
		            	items: owlsl_ops.items,
		            },
		        };

		        if (owlsl_ops.items >= 5) {
		        	responsive_value = {
			            0:{
			              	items:1,
			              	dot:true,
			            },
			            767:{
			              	items:2,
			            },
			            1024: {
			            	items: 3,
			            },
			            1200: {
			            	items: owlsl_ops.items - 1,
			            },
			            1320: {
			            	items: owlsl_ops.items,
			            },
			        };
		        }
		        
		        owlsl.owlCarousel({
		          	stagePadding: owlsl_ops.stagePadding,
		          	margin: owlsl_ops.margin,
		          	items: owlsl_ops.items,
		          	loop: owlsl_ops.loop,
		          	autoplay: owlsl_ops.autoplay,
		          	autoplayTimeout: owlsl_ops.autoplayTimeout,
		          	thumbs: owlsl_ops.thumbs,
		          	dots:owlsl_ops.dots,
		          	nav:false,
		          	autoplayHoverPause: owlsl_ops.autoplayHoverPause,
		          	slideBy: owlsl_ops.slideBy,
		          	smartSpeed: owlsl_ops.smartSpeed,
		          	rtl: owlsl_ops.rtl,
		          	responsive: responsive_value,
		        });

		      	/* Fixed WCAG */
				owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
				owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
				owlsl.find(".owl-dots button").attr("title", "Dots");

		    });
	    	
        });
        
   });

})(jQuery);