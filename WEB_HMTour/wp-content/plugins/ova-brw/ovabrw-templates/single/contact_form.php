<?php 
/**
 * The template for displaying contact form 7 within single
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/single/contact_form.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit();

global $product;

// if the product type isn't ovabrw_car_rental
if( ! $product || $product->get_type() !== 'ovabrw_car_rental' ) return;

$pid = $product->get_id();

?>


<div class="ova-contact-form-tabs-update">
	<?php

		
		$manage_contact_form = get_post_meta( $pid, 'ovabrw_manage_extra_tab', true );

		switch( $manage_contact_form ) {
			case 'in_setting' : {
				$short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
				break;
			}
			case 'new_form' : {
				$short_code_form = get_post_meta( $pid, 'ovabrw_extra_tab_shortcode', true );
				break;
			}

			case 'no' : {
				$short_code_form = '';
				break;
			}

			default: {
				$short_code_form = ovabrw_get_setting( get_option('ova_brw_extra_tab_shortcode_form', '') );
				break;
			}

		}


		echo do_shortcode( htmlspecialchars_decode($short_code_form) ) ;
	?>
</div>