(function($){
	"use strict";
	
	$(window).on('elementor/frontend/init', function () {
		
        elementorFrontend.hooks.addAction('frontend/element_ready/tripgo_elementor_countdown.default', function(){
	       
	        /* Add your code here */

        	var dataDate 	= JSON.parse( $('.ova-countdown').attr('data-date') );
        	var year 		= parseInt( dataDate.year );
        	var month 		= parseInt( dataDate.month );
        	var day 		= parseInt( dataDate.day );
        	var hours 		= parseInt( dataDate.hours );
        	var minutes 	= parseInt( dataDate.minutes );
        	var timezone 	= dataDate.timezone;
        	var textDay 	= dataDate.textDay;
        	var textHour 	= dataDate.textHour;
        	var textMin 	= dataDate.textMin;
        	var textSec 	= dataDate.textSec;
			var austDay 	= new Date(); 
			austDay 		= new Date(year, month - 1, day, hours, minutes); 

			$('.ova-countdown').countdown({until: austDay,timezone: timezone,
				layout :`<div class="item">
							<div class="number">{dnn}</div>
							<div class="text">${textDay}</div>
						</div>
						<div class="item">
							<div class="number">{hnn}</div>
							<div class="text">${textHour}</div>
						</div>
						<div class="item">
							<div class="number">{mnn}</div>
							<div class="text">${textMin}</div>
						</div>
						<div class="item">
							<div class="number">{snn}</div>
							<div class="text">${textSec}</div>
						</div>`});
	        });
        
   });

})(jQuery);
