<?php if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'OVADESTINATION_assets' ) ) {

	class OVADESTINATION_assets{

		public function __construct(){
            /* Add JS, CSS for admin */
			add_action( 'admin_enqueue_scripts', array( $this, 'ovadestination_admin_enqueue_scripts' ), 10, 0 );
            
            /* Add JS, CSS for frontend */
			add_action( 'wp_enqueue_scripts', array( $this, 'ovadestination_enqueue_scripts' ), 10, 0 );

			/* Add JS for Elementor */
			add_action( 'elementor/frontend/after_register_scripts', array( $this, 'ova_enqueue_scripts_elementor_destination' ) );
		}

		public function ovadestination_admin_enqueue_scripts(){
			// Add JS
			wp_enqueue_script( 'script-admin-destination', OVADESTINATION_PLUGIN_URI. 'assets/js/script-admin.js', [ 'jquery' ], false, true );		

			// Init Css
			wp_enqueue_style( 'destination_style', OVADESTINATION_PLUGIN_URI.'assets/css/admin-style.css' );
		}

		public function ovadestination_enqueue_scripts(){
			// Imagesloaded
			wp_enqueue_script( 'script-destination-imagesloaded', OVADESTINATION_PLUGIN_URI. 'assets/libs/imagesloaded/imagesloaded.min.js', [ 'jquery' ], false, true );

			// Masonry 
			wp_enqueue_script( 'script-destination-masonry', OVADESTINATION_PLUGIN_URI. 'assets/libs/masonry/masonry.min.js', [ 'jquery' ], false, true );

			// Add JS
			wp_enqueue_script( 'script-destination', OVADESTINATION_PLUGIN_URI. 'assets/js/script.js', [ 'jquery' ], false, true );	

			// Fontawesome
			if( is_post_type_archive('destination') ) {
				wp_enqueue_style('fontawesome', OVADESTINATION_PLUGIN_URI.'/assets/libs/fontawesome/css/all.min.css', array(), null);	
			}

			// Init Css
			wp_enqueue_style( 'destination_style', OVADESTINATION_PLUGIN_URI.'assets/css/style.css' );

			// Map
			if ( get_option( 'ova_brw_google_key_map', false ) ) {
				wp_enqueue_script( 'ovabrw-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
			} else {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true );
			}

			// Fancybox
			wp_enqueue_script( 'script-destination-fancybox', OVADESTINATION_PLUGIN_URI. 'assets/libs/fancybox/fancybox.umd.js', [ 'jquery' ], false, true );
			wp_enqueue_style( 'destination_fancybox_style', OVADESTINATION_PLUGIN_URI.'assets/libs/fancybox/fancybox.css' );
		}

		// Add JS for elementor
		public function ova_enqueue_scripts_elementor_destination(){
			wp_enqueue_script( 'script-elementor-destination', OVADESTINATION_PLUGIN_URI. 'assets/js/script-elementor.js', [ 'jquery' ], false, true );
		}
	}
	
	new OVADESTINATION_assets();
}
