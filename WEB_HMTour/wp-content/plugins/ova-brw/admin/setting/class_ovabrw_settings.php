<?php if ( ! defined( 'ABSPATH' ) ) exit();

if ( ! class_exists( 'Ovabrw_Settings' ) ){
    class Ovabrw_Settings{
        public function __construct() {
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'ovabrw_add_settings_tab' ), 10, 1 );
        }

        public function ovabrw_add_settings_tab( $settings ) {
		  	$settings[] = include( OVABRW_PLUGIN_PATH.'/admin/setting/ovabrw-settings-tab.php' );  

		  	return $settings;
		}
    }
}

new Ovabrw_Settings();