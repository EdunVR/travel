(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
		
        
		elementorFrontend.hooks.addAction('frontend/element_ready/tripgo_elementor_testimonial.default', function(){

			$(".ova-testimonial .slide-testimonial-version1").each(function(){
		        var slk = $(this) ;
		        var slk_ops = slk.data('options') ? slk.data('options') : {};

		        var isRTL = $('body').hasClass('rtl');
		        if ( isRTL ) {
        			slk.parent().attr('dir','rtl');
        		}
		        
		        slk.slick({
		            dots: false,
		            autoplay : slk_ops.autoplay,
		            autoplaySpeed : slk_ops.autoplay_speed, 
		            speed: slk_ops.smartSpeed,
				    centerPadding: 0,
				    slidesToShow: 1,
				    infinite: slk_ops.loop,
				    arrows: false,
				    dots: slk_ops.dots,
				    pauseOnHover: slk_ops.pause_on_hover,
		            variableWidth: false,
				    centerMode: true,
				    asNavFor: '.slide-for',
				    rtl: isRTL,
				    responsive: [
				    {
				      breakpoint: 768,
				      settings: {
				        arrows: true,
				        centerMode: true,
				        variableWidth: false,
				      }
				    },
				  ]
				});

		      	/* Fixed WCAG */
				slk.find(".slick-prev").attr("title", "Previous");
				slk.find(".slick-next").attr("title", "Next");
				slk.find(".slick-dots button").attr("title", "Dots");

		    });

		    //slide syncing
		    $(".slide-for").each(function(){
		        var slk2 = $(this) ;
		        
		        // slider syncing
			    slk2.slick({ 
				    slidesToShow: 3,
				    slidesToScroll: 1,
				    arrows: false,
				    dots: false,
				    variableWidth: true,
				    fade: true,
                    focusOnSelect: true,
                    centerMode: true,
				    asNavFor: '.slide-testimonial-version1'
				});

		    });
		});


   });

})(jQuery);
