<?php
/*
Plugin Name: Ovatheme Destination
Plugin URI: https://themeforest.net/user/ovatheme
Description: Destination
Author: Ovatheme
Version: 1.1.1
Author URI: https://themeforest.net/user/ovatheme/portfolio
Text Domain: ova-destination
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) exit();


if ( ! class_exists( 'OvaDestination') ) {
	class OvaDestination{
		function __construct() {
			$this -> define_constants();
			$this -> includes();
			$this -> supports();
		}

		function define_constants(){

			if (!defined('OVADESTINATION_PLUGIN_FILE')) {
                define( 'OVADESTINATION_PLUGIN_FILE', __FILE__ );   
            }

            if (!defined('OVADESTINATION_PLUGIN_URI')) {
                define( 'OVADESTINATION_PLUGIN_URI', plugin_dir_url( __FILE__ ) );   
            }

            if (!defined('OVADESTINATION_PLUGIN_PATH')) {
                define( 'OVADESTINATION_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );   
            }
			
			
			load_plugin_textdomain( 'ova-destination', false, basename( dirname( __FILE__ ) ) .'/languages' );
		}
		

		function includes() {

			// inc
			require_once( OVADESTINATION_PLUGIN_PATH.'inc/class-ova-custom-post-type.php' );

			require_once( OVADESTINATION_PLUGIN_PATH.'inc/class-ova-get-data.php' );

			require_once( OVADESTINATION_PLUGIN_PATH.'inc/ova-core-functions.php' );
			
			require_once( OVADESTINATION_PLUGIN_PATH.'inc/class-ova-templates-loaders.php' );

			require_once( OVADESTINATION_PLUGIN_PATH.'inc/class-ova-assets.php' );


			// admin
			require_once( OVADESTINATION_PLUGIN_PATH.'admin/class-ova-metabox.php' );
			require_once( OVADESTINATION_PLUGIN_PATH.'admin/class-cmb2-field-map.php' );

			/* Customize */
			require_once OVADESTINATION_PLUGIN_PATH.'/inc/class-customize.php';

		}


		function supports() {

			/* Make Elementors */
			if ( did_action( 'elementor/loaded' ) ) {
				include OVADESTINATION_PLUGIN_PATH.'elementor/class-ova-register-elementor.php';
			}

		}

	}
}


return new OvaDestination();