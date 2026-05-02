(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

        /* Elementor product filter ajax  */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_filter_ajax.default', function(){

            $('.ova-product-filter-ajax').each( function(){

                var that    = $(this);
                var btn     = that.find('.product-filter-button');
                
                var show_on_sale   = that.data('show_on_sale');
                var args_show      = JSON.parse(that.attr('data-args_show'));
                var posts_per_page = that.data('posts_per_page');
                var orderby        = that.data('orderby');
                var order          = that.data('order');

                //load init
                product_filter_load_ajax('all',show_on_sale, args_show, posts_per_page, orderby, order);
                
                btn.each( function() {
                    $(this).on('click', function() {

                        that.find(".product-filter-button").removeClass("active-category");
                        $(this).addClass("active-category");

                        var term = $(this).attr( "data-slug" );
                        product_filter_load_ajax(term, show_on_sale, args_show, posts_per_page, orderby, order);

                    });
                });  

            });

        });
    
        function product_filter_ajax_slide() {
            $(".ova-product-filter-ajax .slide-product").each(function(){
                var owlsl     = $(this);
                var owlsl_ops = owlsl.data('options') ? owlsl.data('options') : {};

                if ( $('body').hasClass('rtl') ) {
                    owlsl_ops.rtl = true;
                }

                var responsive_value = {
                    0:{
                        items:1,
                        slideBy: 1,
                        nav:false,
                        dots: true,
                    },
                    767:{
                        items: 1,
                    },
                };
              
                owlsl.owlCarousel({
                    margin: 0,
                    items: 1,
                    loop: owlsl_ops.loop,
                    autoplay: owlsl_ops.autoplay,
                    autoplayTimeout: owlsl_ops.autoplayTimeout,
                    nav: owlsl_ops.nav,
                    dots: owlsl_ops.dots,
                    thumbs: owlsl_ops.thumbs,
                    autoplayHoverPause: owlsl_ops.autoplayHoverPause,
                    slideBy: owlsl_ops.slideBy,
                    smartSpeed: owlsl_ops.smartSpeed,
                    rtl:owlsl_ops.rtl,
                    navText:[
                        '<i class="icomoon icomoon-angle-left" ></i>',
                        '<i class="icomoon icomoon-angle-right" ></i>'
                    ],
                    responsive: responsive_value
                });

                /* Fixed WCAG */
                owlsl.find(".owl-nav button.owl-prev").attr("title", "Previous");
                owlsl.find(".owl-nav button.owl-next").attr("title", "Next");
                owlsl.find(".owl-dots button").attr("title", "Dots");
            });
        }

        function product_filter_load_ajax(term,show_on_sale,args_show,posts_per_page,orderby,order){
            
            $.ajax({
               url: ajax_object.ajax_url,
               type: 'POST',
               data: ({
                   action: 'ovabrw_load_product_filter',
                   term: term,
                   show_on_sale: show_on_sale,
                   args_show: args_show,
                   posts_per_page: posts_per_page,
                   orderby: orderby,
                   order: order
                }),
                success: function(data){
                    if ( data != '' ){
                        
                        $('.ova-product-filter-ajax .content-item').empty();
                        $('.ova-product-filter-ajax .content-item').append(data).fadeIn(300);
                        $('.ova-product-filter-ajax .content-item').trigger('destroy.owl.carousel');
                         
                        product_filter_ajax_slide();
                    }
                }
            });
        }  
           
	});
})(jQuery);