(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

        /* Product categories */ 
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_categories.default', function(){

            $(".ova_product_categories").each(function(){
                var owlsl     = $(this) ;
                var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

                if ( $('body').hasClass('rtl') ) {
                    owlsl_ops.rtl = true;
                }

                var responsive_value = {
                    0:{
                        items:1,
                        nav:false,
                        dots: true,
                    },
                    479:{
                        items:2,
                        margin: 20,
                        nav:false,
                        dots: true,
                    },
                    767:{
                        items:2,
                        margin: 20,
                    },
                    1024:{
                        items:3
                    },
                    1300:{
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