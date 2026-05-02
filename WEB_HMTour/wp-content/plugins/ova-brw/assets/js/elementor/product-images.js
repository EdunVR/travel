(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

		/* Gallery Images - Slideshow */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_images.default', function(){

            $('.elementor-product-image .ova-gallery-slideshow').each( function() {
                var that    = $(this);
                var options = that.data('options') ? that.data('options') : {};

                 if ( $('body').hasClass('rtl') ) {
                    options.rtl = true;
                }

                var responsive_value = {
                    0:{
                        items:1,
                        nav:false,
                        slideBy: 1,
                    },
                    768:{
                        items: 2,
                        slideBy: 1,
                    },
                    1025:{
                        items: 3,
                        slideBy: 1,
                    },
                    1300:{
                        items: options.items,
                    }
                };
                
                that.owlCarousel({
                    autoWidth: options.autoWidth,
                    margin: options.margin,
                    items: options.items,
                    loop: options.loop,
                    autoplay: options.autoplay,
                    autoplayTimeout: options.autoplayTimeout,
                    center: options.center,
                    lazyLoad: options.lazyLoad,
                    nav: options.nav,
                    dots: options.dots,
                    autoplayHoverPause: options.autoplayHoverPause,
                    slideBy: options.slideBy,
                    smartSpeed: options.smartSpeed,
                    rtl: options.rtl,
                    navText:[
                        '<i aria-hidden="true" class="'+ options.nav_left +'"></i>',
                        '<i aria-hidden="true" class="'+ options.nav_right +'"></i>'
                    ],
                    responsive: responsive_value,
                });

                that.find('.gallery-fancybox').off('click').on('click', function() {
                    var index = $(this).data('index');
                    var gallery_data = $(this).closest('.ova-gallery-popup').find('.ova-data-gallery').data('gallery');

                    Fancybox.show(gallery_data, {
                        Image: {
                            Panzoom: {
                                zoomFriction: 0.7,
                                maxScale: function () {
                                    return 3;
                                },
                            },
                        },
                        startIndex: index,
                    });
                });
            });
        });

	});
})(jQuery);