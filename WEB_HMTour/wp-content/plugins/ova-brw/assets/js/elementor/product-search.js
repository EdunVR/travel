(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

        /* Search Form */
        $(".ovabrw-search .ovabrw-search-form").each(function(){
            var that = $(this);
            var guestspicker = that.find('.ovabrw-guestspicker');
            var guestspicker_control = $(this).find('.guestspicker-control')

            guestspicker.on('click', function() {
                var guestspicker_control = $(this).closest('.guestspicker-control').toggleClass('active');
            });

            $(window).click( function(e) {
                var guestspicker_content = $('.ovabrw-guestspicker-content');
                if ( !guestspicker.is(e.target) && guestspicker.has(e.target).length === 0 && !guestspicker_content.is(e.target) && guestspicker_content.has(e.target).length === 0 ) {
                    guestspicker_control.removeClass('active');
                }
            });

            var minus = that.find('.minus');
            minus.on('click', function() {
                gueststotal($(this), 'sub');
            });

            var plus = that.find('.plus');
            plus.on('click', function() {
                gueststotal($(this), 'sum');
            });

            // select 2
            $('#brw-destinations-select-box, .brw_custom_taxonomy_dropdown').select2({ 
                width: '100%',
            });

        });

        function gueststotal( that, cal ) {
            var guests_button = that.closest('.guests-button');
            var input   = guests_button.find('input[type="text"]');
            var value   = input.val();
            var min     = input.attr('min');
            var max     = input.attr('max');

            if ( cal == 'sub' && parseInt(value) > parseInt(min) ) {
                input.val(parseInt(value) - 1);
            }

            if ( cal == 'sum' && parseInt(value) < parseInt(max) ) {
                input.val(parseInt(value) + 1);
            }

            var guestspicker_control = that.closest('.guestspicker-control');
            var adults = guestspicker_control.find('.ovabrw_adults').val();

            if ( typeof adults === "undefined" || ! adults ) adults = 0;

            var childrens = guestspicker_control.find('.ovabrw_childrens').val();

            if ( typeof childrens === "undefined" || ! childrens ) childrens = 0;

            var babies = guestspicker_control.find('.ovabrw_babies').val();

            if ( typeof babies === "undefined" || ! babies ) babies = 0;

            var gueststotal = guestspicker_control.find('.gueststotal');

            if ( gueststotal ) {
                gueststotal.text( parseInt(adults) + parseInt(childrens) + parseInt(babies) );
            }
        }

	});
})(jQuery);