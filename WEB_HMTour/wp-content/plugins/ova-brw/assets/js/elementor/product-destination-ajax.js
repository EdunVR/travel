(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
        /* Product Destination Ajax */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_destination_ajax.default', function(){
            $('.ovabrw-destination-ajax').each( function() {
                loadProducts($(this));
            });

            $('.ovabrw-destination-ajax .ovabrw-destination-list .destination-item').on( 'click', function(e) {
                e.preventDefault();

                if ( ! $(this).hasClass('active') ) {
                    $(this).closest('.ovabrw-destination-list').find('.destination-item').removeClass('active');
                    $(this).addClass('active');
                    $(this).closest('.ovabrw-destination-ajax').find('.page-numbers').removeClass('current');

                    var categoryAjax = $(this).closest('.ovabrw-destination-ajax');
                    loadProducts(categoryAjax);
                }
            });

            $(document).on('click', '.ovabrw-destination-ajax .ovabrw-destination-products .ovabrw-pagination-ajax .page-numbers', function(e) {
                e.preventDefault();
                var current = $(this).closest('.ovabrw-pagination-ajax').data('paged');
                var paged   = $(this).data('paged');

                if ( current != paged ) {
                    $(window).scrollTop(0);
                    $(this).closest('.ovabrw-pagination-ajax').find('.page-numbers').removeClass('current');
                    $(this).addClass('current');

                    var categoryAjax = $(this).closest('.ovabrw-destination-ajax');
                    loadProducts(categoryAjax);
                }
            });

            function loadProducts( that ) {
                if ( that ) {
                    var result  = that.find('.ovabrw-destination-products');
                    var destinationID = that.find('.destination-item.active').data('destination-id');
                    var loading = that.find('.wrap-load-more');

                    var dataInput       = that.find('input[name="destination-ajax-input"]');
                    var postsPerPage    = dataInput.data('posts-per-page');
                    var order           = dataInput.data('order');
                    var orderBy         = dataInput.data('orderby');
                    var layout          = dataInput.data('layout');
                    var column          = dataInput.data('column');
                    var thumbnailType   = dataInput.data('thumbnail-type');
                    var pagination      = dataInput.data('pagination');
                    var paged           = that.find('.ovabrw-pagination-ajax .current').data('paged');

                    loading.show();

                    $.ajax({
                        url: ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'ovabrw_product_destination_ajax',
                            destination_id: destinationID,
                            posts_per_page: postsPerPage,
                            paged: paged,
                            order: order,
                            orderBy: orderBy,
                            layout: layout,
                            column: column,
                            thumbnail_type: thumbnailType,
                            pagination: pagination
                        },
                        success:function(response) {
                            if ( response ) {
                                var json = JSON.parse( response );
                                result.html(json.result).fadeOut(300).fadeIn(500);

                                product_gallery_slider();
                            }

                            loading.hide();
                        },
                    });
                }
            }

            function product_gallery_slider() {
                $('.ova-gallery-slideshow').each( function() {
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
            }
        });
           
	});
})(jQuery);