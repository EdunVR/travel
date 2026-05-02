<?php
	if(defined('TRIPGO_URL') 	== false) 	define('TRIPGO_URL', get_template_directory());
	if(defined('TRIPGO_URI') 	== false) 	define('TRIPGO_URI', get_template_directory_uri());

	load_theme_textdomain( 'tripgo', TRIPGO_URL . '/languages' );

	// Main Feature
	require_once( TRIPGO_URL.'/inc/class-main.php' );

	// Functions
	require_once( TRIPGO_URL.'/inc/functions.php' );

	// Hooks
	require_once( TRIPGO_URL.'/inc/class-hook.php' );

	// Widget
	require_once (TRIPGO_URL.'/inc/class-widgets.php');
	

	// Elementor
	if (defined('ELEMENTOR_VERSION')) {
		require_once (TRIPGO_URL.'/inc/class-elementor.php');
	}
	
	// WooCommerce
	if (class_exists('WooCommerce')) {
		require_once (TRIPGO_URL.'/inc/class-woo.php');
		require_once (TRIPGO_URL.'/inc/class-woo-template-functions.php');
		require_once (TRIPGO_URL.'/inc/class-woo-template-hooks.php');
	}
	
	
	/* Customize */
	if( current_user_can('customize') ){
	    require_once TRIPGO_URL.'/customize/custom-control/google-font.php';
	    require_once TRIPGO_URL.'/customize/custom-control/heading.php';
	    require_once TRIPGO_URL.'/inc/class-customize.php';
	}
    
   
	require_once ( TRIPGO_URL.'/install-resource/active-plugins.php' );
	
//Remove Checkout Fileds
function quadlayers_remove_checkout_fields( $fields ) {

 unset($fields['billing']['billing_company']);
 unset($fields['billing']['billing_last_name']);
 unset($fields['billing']['billing_address_2']);
 unset($fields['billing']['billing_city']);
 unset($fields['billing']['billing_postcode']);
 unset($fields['billing']['billing_country']);
 unset($fields['billing']['billing_state']);
	
 return $fields; 
}
add_filter( 'woocommerce_checkout_fields' , 'quadlayers_remove_checkout_fields' );

	
	