(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {

        /* Tour Map JS */
        elementorFrontend.hooks.addAction('frontend/element_ready/ovabrw_product_map.default', function(){

            if ( $('.tripgo-tour-map').length > 0 ) {
                $('.tripgo-tour-map').each(function() {
                    var that        = $(this);
                    var input       = $('#pac-input')[0];
                    var address     = that.find('.address');
                    var latitude    = address.attr('latitude');
                    var longitude   = address.attr('longitude');
                    var zoom        = address.data('zoom');

                    if ( ! zoom ) zoom = 17;
                    
                    if ( typeof google !== 'undefined' && latitude && longitude ) {
                        var map = new google.maps.Map( $('#tour-show-map')[0], {
                            center: {
                                lat: parseFloat(latitude),
                                lng: parseFloat(longitude)
                            },
                            zoom: zoom,
                            gestureHandling: 'cooperative',
                        });

                        var autocomplete = new google.maps.places.Autocomplete(input);

                        autocomplete.bindTo('bounds', map);

                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                        var mapIWcontent = $('#pac-input').val();
                        var infowindow = new google.maps.InfoWindow({
                           content: mapIWcontent,
                        });

                        var marker = new google.maps.Marker({
                           map: map,
                           position: map.getCenter(),
                        });

                        marker.addListener('click', function() {
                           infowindow.open(map, marker);
                        });
                    }

                });
            }

        });
           
	});
})(jQuery);