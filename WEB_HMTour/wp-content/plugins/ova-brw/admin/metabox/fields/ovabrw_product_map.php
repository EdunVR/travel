<?php

$ovabrw_latitude 	= $post_id ? get_post_meta( $post_id, 'ovabrw_latitude', true ) : '';
$ovabrw_longitude 	= $post_id ? get_post_meta( $post_id, 'ovabrw_longitude', true ) : '';

if ( ! $ovabrw_latitude ) {
	$ovabrw_latitude = get_option( 'ova_brw_latitude_map_default', 39.177972 );
}

if ( ! $ovabrw_longitude ) {
	$ovabrw_longitude = get_option( 'ova_brw_longitude_map_default', -100.36375 );
}

// Map name
woocommerce_wp_text_input(
  array(
   'id' 				=> 'ovabrw_map_name',
   'class' 				=> 'map_name',
   'label' 				=> esc_html__( 'Map Name', 'ova-brw' ),
   'desc_tip' 			=> 'true',
   'type' 				=> 'hidden',
   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_map_name', true ) : '',
   'custom_attributes' 	=> array(
		'autocomplete' 	=> 'nope',
		'autocorrect'	=> 'off',
		'autocapitalize'=> 'none'
	),
));

// Address
woocommerce_wp_text_input(
  array(
   'id' 				=> 'ovabrw_address',
   'class' 				=> 'address',
   'label' 				=> esc_html__( 'Address', 'ova-brw' ),
   'desc_tip' 			=> 'true',
   'type' 				=> 'hidden',
   'value' 				=> $post_id ? get_post_meta( $post_id, 'ovabrw_address', true ) : '',
   'custom_attributes' 	=> array(
		'autocomplete' 	=> 'nope',
		'autocorrect'	=> 'off',
		'autocapitalize'=> 'none'
	),
));

// Latitude
woocommerce_wp_text_input(
  array(
   'id' 				=> 'ovabrw_latitude',
   'class' 				=> 'latitude',
   'label' 				=> esc_html__( 'Latitude', 'ova-brw' ),
   'desc_tip' 			=> 'true',
   'type' 				=> 'hidden',
   'value' 				=> $ovabrw_latitude,
   'custom_attributes' 	=> array(
		'autocomplete' 	=> 'nope',
		'autocorrect'	=> 'off',
		'autocapitalize'=> 'none'
	),
));

// Longitude
woocommerce_wp_text_input(
  array(
   'id' 				=> 'ovabrw_longitude',
   'class' 				=> 'longitude',
   'label' 				=> esc_html__( 'Longitude', 'ova-brw' ),
   'desc_tip' 			=> 'true',
   'type' 				=> 'hidden',
   'value' 				=> $ovabrw_longitude,
   'custom_attributes' 	=> array(
		'autocomplete' 	=> 'nope',
		'autocorrect'	=> 'off',
		'autocapitalize'=> 'none'
	),
));
