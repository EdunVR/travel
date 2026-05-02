(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

		/* Product Forms */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_forms.default', function(){
           
            $('.ova-forms-product').each( function() {
                var that = $(this);
                var item = that.find('.tabs .item');

                if ( item.length > 0 ) {
                    item.each( function( index ) {
                        if ( index == 0 ) {
                            $(this).addClass('active');
                            var id = $(this).data('id');
                            $(id).show();
                        }
                    });
                }

                item.on('click', function() {
                    item.removeClass('active');
                    $(this).addClass('active');
                    var id = $(this).data('id');

                    if ( id == '#booking-form' ) {
                        that.find('#request-form').hide();
                    }

                    if ( id == '#request-form' ) {
                        that.find('#booking-form').hide();
                    }
                    
                    $(id).show();
                });
            });
        });

	});
})(jQuery);