(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
		
        
		elementorFrontend.hooks.addAction('frontend/element_ready/tripgo_elementor_testimonial_3.default', function(){

			$(".slide-testimonials-version-3").each(function(){
		        var owlsl = $(this) ;
		        var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

		        var responsive_value = {
		            0:{
		              items:1,
		              nav:false
		            },
		            576:{
		              items:1

		            },
		            992:{
		              items:1
		            },
		            1170:{
		              items:owlsl_ops.items
		            }
		        };
		        
		        owlsl.owlCarousel({
			        autoWidth: owlsl_ops.autoWidth,
			        margin: owlsl_ops.margin,
			        items: owlsl_ops.items,
			        loop: owlsl_ops.loop,
			        autoplay: owlsl_ops.autoplay,
			        autoplayTimeout: owlsl_ops.autoplayTimeout,
			        center: owlsl_ops.center,
			        nav: owlsl_ops.nav,
			        dots: owlsl_ops.dots,
			        thumbs: owlsl_ops.thumbs,
			        autoplayHoverPause: owlsl_ops.autoplayHoverPause,
			        slideBy: owlsl_ops.slideBy,
			        smartSpeed: owlsl_ops.smartSpeed,
			        rtl: owlsl_ops.rtl,
				    animateIn: 'fadeIn', 
				    animateOut: 'fadeOut', 
		            responsive: responsive_value,
		        });

		      	/* Fixed WCAG */
				owlsl.find(".owl-dots button").attr("title", "Dots");

		      });
		});


   });

})(jQuery);
