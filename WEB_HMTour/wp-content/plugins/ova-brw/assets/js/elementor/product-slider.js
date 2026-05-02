(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

        /* Product slider */ 
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_slider.default', function(){

            $(".ova-product-slider").each(function(){
                var owlsl      = $(this) ;
                var owlsl_ops  = owlsl.data('options') ? owlsl.data('options') : {};

                if ( $('body').hasClass('rtl') ) {
                    owlsl_ops.rtl = true;
                }

                var responsive_value = {
                    0:{
                        items:1,
                        dots: true,
                    },
                    767: {
                        items:2,
                    },
                    960:{
                        items:owlsl_ops.items - 1,
                    },
                    1200:{
                        items:owlsl_ops.items
                    }
                };
                
                owlsl.owlCarousel({
                    margin: owlsl_ops.margin,
                    items: owlsl_ops.items,
                    loop: owlsl_ops.loop,
                    autoplay: owlsl_ops.autoplay,
                    autoplayTimeout: owlsl_ops.autoplayTimeout,
                    nav: owlsl_ops.nav,
                    dots: owlsl_ops.dots,
                    autoplayHoverPause: owlsl_ops.autoplayHoverPause,
                    slideBy: owlsl_ops.slideBy,
                    smartSpeed: owlsl_ops.smartSpeed,
                    rtl: owlsl_ops.rtl,
                    navText:[
                        '<i class="icomoon icomoon-pre-small"></i>',
                        '<i class="icomoon icomoon-next-small"></i>'
                    ],
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