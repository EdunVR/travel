(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
        /* Product Category Ajax */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_category_ajax.default', function(){
            $('.ovabrw-category-ajax').each( function() {
                loadProducts($(this));
            });

            $('.ovabrw-category-ajax .ovabrw-category-list .category-item').on( 'click', function(e) {
                e.preventDefault();

                if ( ! $(this).hasClass('active') ) {
                    $(this).closest('.ovabrw-category-list').find('.category-item').removeClass('active');
                    $(this).addClass('active');
                    $(this).closest('.ovabrw-category-ajax').find('.page-numbers').removeClass('current');

                    var categoryAjax = $(this).closest('.ovabrw-category-ajax');
                    loadProducts(categoryAjax);
                }
            });

            $(document).on('click', '.ovabrw-category-ajax .ovabrw-category-products .ovabrw-pagination-ajax .page-numbers', function(e) {
                e.preventDefault();
                var current = $(this).closest('.ovabrw-pagination-ajax').data('paged');
                var paged   = $(this).data('paged');

                if ( current != paged ) {
                    $(window).scrollTop(0);
                    $(this).closest('.ovabrw-pagination-ajax').find('.page-numbers').removeClass('current');
                    $(this).addClass('current');

                    var categoryAjax = $(this).closest('.ovabrw-category-ajax');
                    loadProducts(categoryAjax);
                }
            });

            function loadProducts( that ) {
                if ( that ) {
                    var result  = that.find('.ovabrw-category-products');
                    var termID  = that.find('.category-item.active').data('term-id');
                    var loading = that.find('.wrap-load-more');

                    var dataInput       = that.find('input[name="category-ajax-input"]');
                    var postsPerPage    = dataInput.data('posts-per-page');
                    var order           = dataInput.data('order');
                    var orderBy         = dataInput.data('orderby');
                    var layout          = dataInput.data('layout');
                    var grid_template   = dataInput.data('grid_template');
                    var column          = dataInput.data('column');
                    var thumbnailType   = dataInput.data('thumbnail-type');
                    var pagination      = dataInput.data('pagination');
                    var paged           = that.find('.ovabrw-pagination-ajax .current').data('paged');

                    loading.show();

                    $.ajax({
                        url: ajax_object.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'ovabrw_product_category_ajax',
                            term_id: termID,
                            posts_per_page: postsPerPage,
                            paged: paged,
                            order: order,
                            orderBy: orderBy,
                            layout: layout,
                            grid_template: grid_template,
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