<?php defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'OVABRW_Assets' ) ) {

	class OVABRW_Assets {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'ovabrw_admin_enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ovabrw_enqueue_scripts' ), 10, 0 );

			add_action( 'admin_head', array( $this, 'ovabrw_admin_head' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'ovabrw_admin_head' ) );
		}

		public function ovabrw_admin_enqueue_scripts() {
			// Map
			if ( get_option( 'ova_brw_google_key_map', false ) ) {
				wp_enqueue_script( 'ovabrw-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
			} else {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true );
			}

			// Fullcalendar
			wp_enqueue_script( 'moment', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/moment.min.js', array('jquery'), null, true );
		    wp_enqueue_script( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.js', array('jquery'), null, true );
		    wp_enqueue_script( 'locale-all', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.js', array('jquery'), null, true );
		    wp_enqueue_style( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.min.css', array(), null );
			wp_enqueue_script( 'calendar_booking', OVABRW_PLUGIN_URI.'assets/js/admin/calendar.js', array('jquery'), false, true );
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );		
			wp_enqueue_script( 'jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.js', array('jquery'), false, true );
			wp_enqueue_style( 'jquery-timepicker', OVABRW_PLUGIN_URI.'assets/libs/jquery-timepicker/jquery.timepicker.min.css' );

			// Date Time Picker
			wp_enqueue_script( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );

			// Admin Css
			wp_enqueue_style( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css', array(), null );
			wp_enqueue_style( 'ovabrw_admin', OVABRW_PLUGIN_URI.'assets/css/admin/ovabrw_admin.css', array(), null );

			// Admin js
			wp_enqueue_script( 'admin_script', OVABRW_PLUGIN_URI.'assets/js/admin/admin_script.min.js', array('jquery'), false, true );
		    wp_localize_script( 'admin_script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

		    // Add select2
		    if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' && isset( $_GET['page'] ) && $_GET['page'] == 'ovabrw-create-order' ) {
		    	wp_enqueue_style( 'select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', array(), null );
				wp_enqueue_script( 'select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', array('jquery'), null, true );
		    }
		}

		public function ovabrw_enqueue_scripts() {
			// Map
			if ( get_option( 'ova_brw_google_key_map', false ) ) {
				wp_enqueue_script( 'ovabrw-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_option( 'ova_brw_google_key_map', '' ).'&callback=Function.prototype&libraries=places', false, true );
			} else {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?sensor=false&callback=Function.prototype&libraries=places', array('jquery'), false, true );
			}
			
		    wp_enqueue_script( 'jquery-ui-autocomplete' );

		    // Fullcalendar
		    wp_enqueue_script( 'moment', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/moment.min.js', array('jquery'), null, true );
		    wp_enqueue_script( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.js', array('jquery'), null, true );
		    wp_enqueue_script( 'locale-all', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.js', array('jquery'), null, true );
		    wp_enqueue_style( 'fullcalendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/main.min.css', array(), null );

		    // Datepicker
		    wp_enqueue_script( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );
		    wp_enqueue_style( 'datetimepicker', OVABRW_PLUGIN_URI.'assets/libs/datetimepicker/jquery.datetimepicker.css', array(), null );

			// add elegant
			if ( apply_filters( 'ovabrw_use_elegant_font', true ) ) {
				wp_enqueue_style( 'elegant_font', OVABRW_PLUGIN_URI.'assets/libs/elegant_font/style.css', array(), null );	
			}

			wp_enqueue_style( 'ovabrw-frontend', OVABRW_PLUGIN_URI.'assets/css/frontend/ovabrw_frontend.css', array(), null );
			wp_enqueue_script( 'ova_brw_js_frontend', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-frontend.min.js', array('jquery'), null, true );
			wp_localize_script( 'ova_brw_js_frontend', 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php') ) );

			// Add select2
	    	wp_enqueue_style( 'select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.css', array(), null );
			wp_enqueue_script( 'select2', OVABRW_PLUGIN_URI.'assets/libs/select2/select2.min.js', array('jquery'), null, true );

			if( is_singular('product') ) {
				// Jquery tiptip
				wp_enqueue_style( 'jquery-tiptip', OVABRW_PLUGIN_URI.'assets/libs/jquery-tiptip/tipTip.css', array(), null );
				wp_enqueue_script( 'jquery-tiptip', OVABRW_PLUGIN_URI.'assets/libs/jquery-tiptip/jquery-tiptip.min.js', array('jquery'), null, true );
			}
			
		}

		function ovabrw_admin_head() {
			global $wp;

			if ( ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'product' ) || ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) || ( isset( $wp->query_vars['wcfm-products-manage'] ) ) ) {

				// Custom taxonomies choosed in post
				$all_cus_tax 			= array();
				$exist_cus_tax 			= array();
				$cus_tax_hide_p_loaded 	= array();

				// Get All Custom taxonomy
				$ovabrw_custom_taxonomy = ovabrw_create_type_taxonomies();

				// All custom slug tax
				if ( $ovabrw_custom_taxonomy ){
					foreach ( $ovabrw_custom_taxonomy as $key => $value ) {
						array_push($all_cus_tax, $value['slug']);
					}
				}
		
				// Edit product in backend and WCFM plugin
				if ( ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' ) || isset( $wp->query_vars['wcfm-products-manage'] ) ) {
					$id = isset( $_GET['post'] ) ? $_GET['post'] : '';

					if ( ! $id &&  isset( $wp->query_vars['wcfm-products-manage'] ) &&  $wp->query_vars['wcfm-products-manage'] != '' ) {
						$id = $wp->query_vars['wcfm-products-manage'];
					}

					$terms_id = get_the_terms( $id, 'product_cat' );
					
					if ( $terms_id ) {
						foreach ( $terms_id as $key => $term ) {
							$ovabrw_custom_tax = get_term_meta($term->term_id, 'ovabrw_custom_tax', true);	
							
							if ( $ovabrw_custom_tax ) {
								foreach ( $ovabrw_custom_tax as $key => $value ) {
									array_push( $exist_cus_tax, $value );
								}	
							}
						}
					}

					if ( $ovabrw_custom_taxonomy ) {
						foreach ( $ovabrw_custom_taxonomy as $key => $value ) {
							if ( ! in_array( $value['slug'], $exist_cus_tax ) ) {
								array_push( $cus_tax_hide_p_loaded, $value['slug'] );
							}
						}
					}
				} else { // Add new product
					$cus_tax_hide_p_loaded = $all_cus_tax;
				}
				
				// Check show custom taxonomy depend category	
				$ova_brw_search_show_tax_depend_cat = ovabrw_get_setting( get_option( 'ova_brw_search_show_tax_depend_cat', 'yes' ) );

				if ( $ova_brw_search_show_tax_depend_cat == 'no' ) {
					$cus_tax_hide_p_loaded = $all_cus_tax = array();
				}
				
				echo '<script type="text/javascript"> var ova_brw_search_show_tax_depend_cat = "'.$ova_brw_search_show_tax_depend_cat.'"; var cus_tax_hide_p_loaded = "'.implode(',', $cus_tax_hide_p_loaded).'"; var all_cus_tax = "'.implode(',', $all_cus_tax).'"; </script>';
			}
		}
		
	}

	new OVABRW_Assets();
}